<?php

namespace App\Controllers\Atleta;

use App\Models\Atleta;
use App\Models\Competicao;
use App\Models\Prova;
use App\Models\Inscricao;
use Core\Http\Response;
use Core\Attributes\Route\Get;
use Core\Attributes\Route\Post;
use Core\Attributes\Route\Middleware;

#[Middleware(['auth', 'role:atleta'])]
class InscricaoController
{
    /**
     * Lista competições que estão abertas para inscrição.
     */
    #[Get('/atleta/competicoes', name: 'atleta.competicoes.index')]
    public function index()
    {
        $competicoes = (new Competicao())
            ->where('status', '=', 'aberta')
            ->orderBy('data_inicio', 'ASC')
            ->get();

        return view('atleta/inscricoes/index', [
            'title' => 'Competições Disponíveis',
            'competicoes' => $competicoes
        ]);
    }

    /**
     * Lista as inscrições do próprio atleta logado.
     */
    #[Get('/atleta/minhas-inscricoes', name: 'atleta.inscricoes.me')]
    public function me()
    {
        $usuarioId = session('user')['id'];
        $atleta = (new Atleta())->where('usuario_id', '=', $usuarioId)->first();

        if (!$atleta) {
            return Response::makeRedirect('/atleta/perfil');
        }

        $inscricoes = (new Inscricao())
            ->where('atleta_id', '=', $atleta->id)
            ->with(['competicao', 'prova', 'resultado', 'notas.jurado'])
            ->orderBy('id', 'DESC')
            ->get();

        return view('atleta/inscricoes/minhas', [
            'title' => 'Minhas Inscrições',
            'inscricoes' => $inscricoes
        ]);
    }

    /**
     * Mostra os detalhes de uma competição e as provas disponíveis.
     */
    #[Get('/atleta/competicoes/{id}/detalhes', name: 'atleta.competicoes.detalhes')]
    public function detalhes(int $id)
    {
        $usuarioId = session('user')['id'];
        $atleta = (new Atleta())->where('usuario_id', '=', $usuarioId)->first();

        if (!$atleta || !$atleta->categoria_id) {
            session()->flash('message', 'Complete seu perfil com sua categoria antes de se inscrever.');
            return Response::makeRedirect('/atleta/perfil');
        }

        $competicao = (new Competicao())->where('id', '=', $id)->first();

        if (!$competicao || $competicao->status !== 'aberta') {
            return Response::makeRedirect('/atleta/competicoes');
        }

        // Listar provas da categoria do atleta nesta competição
        $provas = (new Prova())
            ->where('competicao_id', '=', $id)
            ->where('categoria_id', '=', $atleta->categoria_id)
            ->get();

        // Calcular vagas ocupadas para cada prova
        foreach ($provas as $prova) {
            $prova->vagas_ocupadas = (new Inscricao())->where('prova_id', '=', $prova->id)->count();
        }

        // Buscar inscrições já realizadas pelo atleta nesta competição
        $minhasInscricoes = (new Inscricao())
            ->where('atleta_id', '=', $atleta->id)
            ->where('competicao_id', '=', $id)
            ->get();

        $idsProvasInscritas = array_map(fn($i) => $i->prova_id, $minhasInscricoes);

        // Filtrar as provas: não mostrar as que ele já se inscreveu
        $provas = array_filter($provas, function($p) use ($idsProvasInscritas) {
            return !in_array($p->id, $idsProvasInscritas);
        });

        return view('atleta/inscricoes/detalhes', [
            'title' => 'Detalhes da Competição',
            'competicao' => $competicao,
            'provas' => $provas,
            'atleta' => $atleta,
            'idsProvasInscritas' => $idsProvasInscritas
        ]);
    }

    /**
     * Processa a inscrição do atleta em uma prova.
     */
    #[Post('/atleta/inscricoes/inscrever', name: 'atleta.inscricoes.store')]
    public function store()
    {
        $usuarioId = session('user')['id'];
        $atleta = (new Atleta())->where('usuario_id', '=', $usuarioId)->first();
        $provaId = (int) request()->get('prova_id');
        $competicaoId = (int) request()->get('competicao_id');

        if (!$atleta || !$atleta->ativo) {
            return Response::makeJson(['status' => 'error', 'message' => 'Você precisa estar com perfil ativo para se inscrever.'], 403);
        }

        // Verificar se a prova é válida e da categoria do atleta
        $prova = (new Prova())->find($provaId);
        if (!$prova || $prova->categoria_id != $atleta->categoria_id || $prova->competicao_id != $competicaoId) {
             return Response::makeJson(['status' => 'error', 'message' => 'Prova inválida ou de categoria diferente da sua.'], 400);
        }

        // Verificar limite de participantes
        if ($prova->max_participantes > 0) {
            $inscritosAtuais = (new Inscricao())->where('prova_id', '=', $provaId)->count();
            if ($inscritosAtuais >= $prova->max_participantes) {
                return Response::makeJson(['status' => 'error', 'message' => 'Desculpe, esta prova já atingiu o limite máximo de inscritos.'], 400);
            }
        }

        // Verificar se já está inscrito
        $existe = (new Inscricao())
            ->where('atleta_id', '=', $atleta->id)
            ->where('prova_id', '=', $provaId)
            ->first();

        if ($existe) {
            return Response::makeJson(['status' => 'error', 'message' => 'Você já está inscrito nesta prova.'], 400);
        }

        // Realizar inscrição
        $inscricao = new Inscricao();
        $inscricao->atleta_id = $atleta->id;
        $inscricao->competicao_id = $competicaoId;
        $inscricao->prova_id = $provaId;
        $inscricao->status = 'confirmada'; // Ou 'pendente' se houver aprovação do admin/treinador
        $inscricao->inscrito_em = date('Y-m-d H:i:s');
        
        if ($inscricao->save()) {
            return Response::makeRedirect(route('atleta.competicoes.detalhes', ['id' => $competicaoId]));
        }

        return Response::makeJson(['status' => 'error', 'message' => 'Erro ao realizar inscrição.'], 500);
    }

    /**
     * Remove uma inscrição (Desinscrever).
     */
    #[Post('/atleta/inscricoes/{id}/deletar', name: 'atleta.inscricoes.destroy')]
    public function destroy(int $id)
    {
        $usuarioId = session('user')['id'];
        $atleta = (new Atleta())->where('usuario_id', '=', $usuarioId)->first();

        if (!$atleta) {
            return Response::makeJson(['status' => 'error', 'message' => 'Atleta não encontrado.'], 404);
        }

        $inscricao = (new Inscricao())->find($id);

        if (!$inscricao || $inscricao->atleta_id !== $atleta->id) {
            return Response::makeJson(['status' => 'error', 'message' => 'Inscrição não encontrada ou não pertence a você.'], 403);
        }

        // Se já tiver resultado, não pode deletar
        $hasResultado = (new \App\Models\Resultado())->where('inscricao_id', '=', $id)->first();
        if ($hasResultado) {
            return Response::makeJson(['status' => 'error', 'message' => 'Não é possível cancelar inscrição de uma prova que já possui nota.'], 403);
        }

        if ($inscricao->delete()) {
            if (request()->isHtmx()) {
                return new Response(""); // Retorna vazio pro HTMX remover o elemento
            }
            return Response::makeRedirect(route('atleta.inscricoes.me'));
        }

        return Response::makeJson(['status' => 'error', 'message' => 'Erro ao cancelar inscrição.'], 500);
    }
}
