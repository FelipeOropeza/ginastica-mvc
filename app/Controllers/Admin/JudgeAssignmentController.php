<?php

namespace App\Controllers\Admin;

use App\Services\Admin\JudgeAssignmentService;
use App\Services\Admin\ProvaService;
use App\DTOs\Admin\JudgeAssignmentDTO;
use Core\Http\Response;
use Core\Attributes\Route\Get;
use Core\Attributes\Route\Post;
use Core\Attributes\Route\Middleware;

#[Middleware(['auth', 'role:admin,operador'])]
class JudgeAssignmentController
{
    protected JudgeAssignmentService $service;
    protected ProvaService $provaService;

    public function __construct(JudgeAssignmentService $service, ProvaService $provaService)
    {
        $this->service = $service;
        $this->provaService = $provaService;
    }

    #[Get('/admin/provas/{id}/designacoes', name: 'admin.provas.designacoes')]
    public function index(int $id)
    {
        $prova = $this->provaService->findById($id);
        $designacoes = $this->service->getDesignacoesByProva($id);
        $jurados = $this->service->getJuradosDisponiveis();

        return view('admin/provas/designacoes', [
            'title' => "Designar Jurados - " . strtoupper($prova->aparelho),
            'prova' => $prova,
            'designacoes' => $designacoes,
            'jurados' => $jurados,
            'criterios' => [
                'nota_d' => 'Dificuldade (D)',
                'nota_e' => 'Execução (E)',
                'arbitro_superior' => 'Árbitro Superior',
                'geral' => 'Geral'
            ]
        ]);
    }

    #[Post('/admin/provas/{id}/designacoes/store', name: 'admin.provas.designacoes.store')]
    public function store(int $id, JudgeAssignmentDTO $dto)
    {
        $this->service->assign($id, $dto);

        return Response::makeRedirect("/admin/provas/{$id}/designacoes");
    }

    #[Post('/admin/designacoes/{id}/delete', name: 'admin.provas.designacoes.delete')]
    public function delete(int $id)
    {
        $designacao = (new \App\Models\JuradoDesignacao())->find($id);
        $provaId = $designacao->prova_id;
        
        $this->service->unassign($id);

        return Response::makeRedirect("/admin/provas/{$provaId}/designacoes");
    }
}
