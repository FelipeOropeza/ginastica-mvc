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
}
