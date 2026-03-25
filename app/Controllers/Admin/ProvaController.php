<?php

namespace App\Controllers\Admin;

use App\Services\Admin\ProvaService;
use App\Services\Admin\CompetitionService;
use App\Models\Categoria;
use App\DTOs\Admin\ProvaDTO;
use Core\Http\Response;
use Core\Attributes\Route\Get;
use Core\Attributes\Route\Post;
use Core\Attributes\Route\Middleware;

#[Middleware(['auth', 'role:admin,operador'])]
class ProvaController
{
    protected ProvaService $provaService;
    protected CompetitionService $competitionService;

    public function __construct(ProvaService $provaService, CompetitionService $competitionService)
    {
        $this->provaService = $provaService;
        $this->competitionService = $competitionService;
    }

    #[Get('/admin/competicoes/{id}/provas', name: 'admin.provas.index')]
    public function index(int $id)
    {
        $competicao = $this->competitionService->findById($id);
        $provas = $this->provaService->getByCompeticao($id);
        $categorias = (new Categoria())->all();

        return view('admin/provas/index', [
            'title' => "Provas: {$competicao->nome}",
            'competicao' => $competicao,
            'provas' => $provas,
            'categorias' => $categorias,
            'aparelhos' => ['solo', 'salto', 'barras_assimetricas', 'trave']
        ]);
    }

    #[Post('/admin/competicoes/{id}/provas/store', name: 'admin.provas.store')]
    public function store(int $id, ProvaDTO $dto)
    {
        $this->provaService->create($id, $dto);

        return Response::makeRedirect("/admin/competicoes/{$id}/provas");
    }

    #[Post('/admin/provas/{id}/deletar', name: 'admin.provas.delete')]
    public function delete(int $id)
    {
        $prova = $this->provaService->findById($id);
        $compId = $prova->competicao_id;
        
        $this->provaService->delete($id);

        return Response::makeRedirect("/admin/competicoes/{$compId}/provas");
    }

    #[Get('/admin/provas/{id}/ordem', name: 'admin.provas.ordem')]
    public function ordem(int $id)
    {
        $prova = $this->provaService->findById($id);
        $competicao = $this->competitionService->findById($prova->competicao_id);
        $inscricoes = (new \App\Models\Inscricao())
            ->where('prova_id', '=', $id)
            ->with(['atleta.equipe'])
            ->orderBy('ordem_apresentacao', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();

        return view('admin/provas/ordem', [
            'title' => "Ordem: " . str_replace('_', ' ', $prova->aparelho),
            'prova' => $prova,
            'competicao' => $competicao,
            'inscricoes' => $inscricoes
        ]);
    }

    #[Post('/admin/provas/{id}/shuffle', name: 'admin.provas.shuffle')]
    public function shuffle(int $id)
    {
        $inscricoes = (new \App\Models\Inscricao())
            ->where('prova_id', '=', $id)
            ->whereIn('status', ['confirmada', 'pendente'])
            ->get();

        if (!empty($inscricoes)) {
            shuffle($inscricoes);
            foreach ($inscricoes as $i => $ins) {
                $ins->ordem_apresentacao = $i + 1;
                $ins->save();
            }
        }

        if (request()->isHtmx()) {
             return $this->ordem($id);
        }

        return Response::makeRedirect("/admin/provas/{$id}/ordem");
    }

    #[Get('/admin/provas/{id}/notas', name: 'admin.provas.notas')]
    public function notas(int $id)
    {
        $prova = $this->provaService->findById($id);
        $competicao = $this->competitionService->findById($prova->competicao_id);

        $inscricoes = (new \App\Models\Inscricao())
            ->where('prova_id', '=', $id)
            ->with(['atleta.equipe', 'resultado'])
            ->orderBy('ordem_apresentacao', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();

        $notas = (new \App\Models\Nota())
            ->whereIn('inscricao_id', array_map(fn($i) => $i->id, $inscricoes))
            ->with(['jurado'])
            ->orderBy('id', 'ASC')
            ->get();

        return view('admin/provas/notas', [
            'title' => "Notas: " . str_replace('_', ' ', $prova->aparelho),
            'prova' => $prova,
            'competicao' => $competicao,
            'inscricoes' => $inscricoes,
            'notas' => $notas,
        ]);
    }

    #[Post('/admin/notas/{id}/reabrir', name: 'admin.notas.reabrir')]
    public function reabrirNota(int $id)
    {
        $nota = (new \App\Models\Nota())->find($id);

        if (!$nota) {
            abort(404);
        }

        $inscricaoId = $nota->inscricao_id;
        $inscricao = (new \App\Models\Inscricao())->with(['prova', 'competicao'])->find($inscricaoId);
        $provaId = $inscricao->prova_id;

        // Bloquear se a competição já foi encerrada
        if ($inscricao->competicao && $inscricao->competicao->status === 'encerrada') {
            fail_validation(['status' => 'A competição já foi encerrada. Os resultados são definitivos.']);
        }

        // Deletar a nota específica
        (new \App\Models\Nota())->delete($id);

        // Recalcular resultado deste atleta (fica parcial = calculado=0)
        $notasRestantes = (new \App\Models\Nota())->where('inscricao_id', '=', $inscricaoId)->get();

        if (empty($notasRestantes)) {
            $resultado = (new \App\Models\Resultado())->where('inscricao_id', '=', $inscricaoId)->first();
            if ($resultado) {
                (new \App\Models\Resultado())->delete($resultado->id);
            }
        } else {
            // Recalcular — o resultado ficará com calculado=0 (faltam notas)
            $avaliacaoService = app(\App\Services\Juiz\AvaliacaoService::class);
            $avaliacaoService->atualizarResultadoFinal($inscricaoId);
        }

        // Recalcular ranking da prova
        \App\Models\Resultado::calcularRanking($provaId);

        // A prova permanece encerrada para todos os outros atletas
        // Apenas este atleta fica com resultado pendente, permitindo re-entrada do juiz

        return Response::makeRedirect("/admin/provas/{$provaId}/notas");
    }
}
