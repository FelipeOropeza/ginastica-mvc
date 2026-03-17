<?php

namespace App\Controllers\Admin;

use App\Services\Admin\CompetitionService;
use App\DTOs\Admin\CompetitionDTO;
use Core\Http\Response;
use Core\Attributes\Route\Get;
use Core\Attributes\Route\Post;
use Core\Attributes\Route\Middleware;

#[Middleware(['auth', 'role:admin,operador'])]
class CompetitionController
{
    protected CompetitionService $service;

    public function __construct(CompetitionService $service)
    {
        $this->service = $service;
    }

    #[Get('/admin/competicoes', name: 'admin.competicoes.index')]
    public function index()
    {
        $competicoes = $this->service->getAll();
        
        return view('admin/competicoes/index', [
            'title' => 'Gestão de Competições',
            'competicoes' => $competicoes
        ]);
    }

    #[Get('/admin/competicoes/criar', name: 'admin.competicoes.create')]
    public function create()
    {
        return view('admin/competicoes/form', [
            'title' => 'Nova Competição'
        ]);
    }

    #[Post('/admin/competicoes/store', name: 'admin.competicoes.store')]
    public function store(CompetitionDTO $dto)
    {
        $this->service->create($dto);

        return Response::makeRedirect('/admin/competicoes');
    }

    #[Get('/admin/competicoes/{id}/editar', name: 'admin.competicoes.edit')]
    public function edit(int $id)
    {
        $competicao = $this->service->findById($id);

        return view('admin/competicoes/form', [
            'title' => 'Editar Competição',
            'competicao' => $competicao
        ]);
    }

    #[Post('/admin/competicoes/{id}/update', name: 'admin.competicoes.update')]
    public function update(int $id, CompetitionDTO $dto)
    {
        $this->service->update($id, $dto);

        return Response::makeRedirect('/admin/competicoes');
    }

    #[Post('/admin/competicoes/{id}/deletar', name: 'admin.competicoes.delete')]
    public function delete(int $id)
    {
        $this->service->delete($id);

        if (request()->isHtmx()) {
            return new Response('');
        }

        return Response::makeRedirect('/admin/competicoes');
    }
}
