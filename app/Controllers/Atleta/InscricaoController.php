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
     * Dashboard principal do atleta com resumo e competições abertas.
     */
    #[Get('/atleta/dashboard', name: 'atleta.dashboard')]
    public function dashboard()
    {
        $usuarioId = session('user')['id'];
        $atleta = (new Atleta())
            ->where('usuario_id', '=', $usuarioId)
            ->with(['equipe', 'categoria'])
            ->first();

        if (!$atleta) {
            return view('atleta/perfil_incompleto', [
                'title' => 'Perfil Incompleto'
            ]);
        }

        // Estatísticas para os cards
        $totalInscricoes = (new Inscricao())->where('atleta_id', '=', $atleta->id)->count();
        $melhorRes = $atleta->melhorNota();
        $melhorNota = $melhorRes ? $melhorRes->nota_final : '--';
        
        // Atividades Recentes (últimas 5)
        $atividades = (new Inscricao())
            ->where('atleta_id', '=', $atleta->id)
            ->with(['competicao', 'prova', 'resultado'])
            ->orderBy('inscrito_em', 'DESC')
            ->limit(5)
            ->get();

        // Competições abertas para inscrição
        $competicoes = (new Competicao())
            ->where('status', '=', 'aberta')
            ->orderBy('data_inicio', 'ASC')
            ->get();

        // Check if profile is missing critical data for auto-enrollment
        $perfilIncompleto = (!$atleta->equipe_id || !$atleta->categoria_id || !$atleta->data_nascimento);

        return view('atleta/dashboard', [
            'title' => 'Painel do Atleta',
            'atleta' => $atleta,
            'totalInscricoes' => $totalInscricoes,
            'melhorNota' => $melhorNota,
            'atividades' => $atividades,
            'competicoes' => $competicoes,
            'perfilIncompleto' => $perfilIncompleto
        ]);
    }

    /**
     * Lista todas as competições.
     */
    #[Get('/atleta/competicoes', name: 'atleta.competicoes.index')]
    public function index()
    {
        $usuarioId = session('user')['id'];
        $atleta = (new Atleta())->where('usuario_id', '=', $usuarioId)->first();

        $competicoes = (new Competicao())
            ->where('status', '!=', 'rascunho')
            ->orderBy('data_inicio', 'DESC')
            ->get();

        return view('atleta/inscricoes/index', [
            'title' => 'Competições',
            'competicoes' => $competicoes,
            'atleta' => $atleta
        ]);
    }

    /**
     * Lista as inscrições do atleta.
     */
    #[Get('/atleta/minhas-inscricoes', name: 'atleta.inscricoes.minhas')]
    public function minhas()
    {
        $usuarioId = session('user')['id'];
        $atleta = (new Atleta())->where('usuario_id', '=', $usuarioId)->first();

        $inscricoes = (new Inscricao())
            ->where('atleta_id', '=', $atleta->id)
            ->with(['competicao', 'prova', 'resultado', 'notas.jurado'])
            ->orderBy('inscrito_em', 'DESC')
            ->get();

        return view('atleta/inscricoes/minhas', [
            'title' => 'Minhas Inscrições',
            'inscricoes' => $inscricoes
        ]);
    }

    /**
     * Retorna o formulário (modal) de inscrição filtrado por categoria.
     */
    #[Get('/atleta/competicoes/{id}/inscrever', name: 'atleta.competicoes.form')]
    public function formInscricao(int $id)
    {
        $usuarioId = session('user')['id'];
        $atleta = (new Atleta())->where('usuario_id', '=', $usuarioId)->first();
        
        $competicao = (new Competicao())->find($id);

        if (!$competicao || $competicao->status !== 'aberta') {
            return new Response("<div class='p-4 text-red-500 font-bold'>Inscrições encerradas ou competição não encontrada.</div>");
        }

        // Provas desta competição que batem com a categoria do atleta
        $provas = (new Prova())
            ->where('competicao_id', '=', $id)
            ->where('categoria_id', '=', $atleta->categoria_id)
            ->get();

        // Contagem de inscrições por prova
        foreach ($provas as $prova) {
            $prova->inscritos_count = (new Inscricao())->where('prova_id', '=', $prova->id)->count();
        }

        // IDs das provas que ele já se inscreveu
        $jaInscritas = (new Inscricao())
            ->where('atleta_id', '=', $atleta->id)
            ->where('competicao_id', '=', $id)
            ->get();
        $inscritoIds = array_map(fn($i) => $i->prova_id, $jaInscritas);

        return view('atleta/partials/modal_inscricao', [
            'competicao' => $competicao,
            'provas' => $provas,
            'atleta' => $atleta,
            'inscritoIds' => $inscritoIds
        ]);
    }

    /**
     * Salva as inscrições do atleta.
     */
    #[Post('/atleta/competicoes/store', name: 'atleta.competicoes.store')]
    public function store()
    {
        $usuarioId = session('user')['id'];
        $atleta = (new Atleta())->where('usuario_id', '=', $usuarioId)->first();
        $provas_id = request()->get('provas_id', []);
        $competicao_id = (int) request()->get('competicao_id');

        if (!$atleta || !$atleta->ativo) {
            return new Response("<div class='mb-4 p-3 bg-red-100 text-red-700 rounded'>Sua conta está inativa ou perfil incompleto.</div>");
        }

        if (empty($provas_id)) {
            return new Response("<div class='mb-4 p-3 bg-red-100 text-red-700 rounded'>Selecione pelo menos uma prova.</div>");
        }

        foreach ($provas_id as $pid) {
            $prova = (new Prova())->find($pid);
            if (!$prova) continue;

            // Validação de categoria (segurança)
            if ($prova->categoria_id != $atleta->categoria_id) continue;

            // Validação de limite de vagas
            if ($prova->max_participantes > 0) {
                $count = (new Inscricao())->where('prova_id', '=', $pid)->count();
                if ($count >= $prova->max_participantes) continue;
            }

            // Evitar duplicidade
            $exist = (new Inscricao())->where('atleta_id', '=', $atleta->id)->where('prova_id', '=', $pid)->first();

            if (!$exist) {
                (new Inscricao())->insert([
                    'atleta_id' => $atleta->id,
                    'competicao_id' => $competicao_id,
                    'prova_id' => $pid,
                    'status' => 'confirmada',
                    'inscrito_em' => date('Y-m-d H:i:s')
                ]);
            }
        }

        if (request()->isHtmx()) {
            return new Response("<script>window.location.reload();</script>");
        }

        return Response::makeRedirect('/atleta/dashboard');
    }

    /**
     * Remove uma inscrição específica.
     */
    #[Post('/atleta/inscricoes/{id}/deletar', name: 'atleta.inscricoes.destroy')]
    public function destroy(int $id)
    {
        $usuarioId = session('user')['id'];
        $atleta = (new Atleta())->where('usuario_id', '=', $usuarioId)->first();

        $inscricao = (new Inscricao())->find($id);

        if (!$inscricao || $inscricao->atleta_id !== $atleta->id) {
            return Response::makeJson(['status' => 'error', 'message' => 'Não autorizado.'], 403);
        }

        // Regra: Bloqueia se a competição já saiu de 'aberta' ou se já tem nota
        $comp = (new Competicao())->find($inscricao->competicao_id);
        $hasRes = (new \App\Models\Resultado())->where('inscricao_id', '=', $id)->first();

        if ($comp->status !== 'aberta' || $hasRes) {
             return Response::makeJson(['status' => 'error', 'message' => 'Não é possível cancelar no status atual.'], 403);
        }

        $inscricao->delete();

        if (request()->isHtmx()) {
            return new Response("<script>window.location.reload();</script>");
        }

        return Response::makeRedirect('/atleta/dashboard');
    }
}
