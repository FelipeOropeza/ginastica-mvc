<?php

namespace App\Controllers\Admin;

use App\Services\Admin\EquipeService;
use App\DTOs\Admin\EquipeDTO;
use Core\Http\Response;
use Core\Attributes\Route\Get;
use Core\Attributes\Route\Post;
use Core\Attributes\Route\Middleware;

#[Middleware(['auth', 'role:admin,operador'])]
class EquipeController
{
    protected EquipeService $service;

    public function __construct(EquipeService $service)
    {
        $this->service = $service;
    }

    #[Get('/admin/equipes', name: 'admin.equipes.index')]
    public function index()
    {
        $equipes = $this->service->getAll();
        return view('admin/equipes/index', [
            'title' => 'Gestão de Equipes',
            'equipes' => $equipes
        ]);
    }

    #[Get('/admin/equipes/criar', name: 'admin.equipes.create')]
    public function create()
    {
        return view('admin/equipes/form', [
            'title' => 'Nova Equipe'
        ]);
    }

    #[Post('/admin/equipes/store', name: 'admin.equipes.store')]
    public function store(EquipeDTO $dto)
    {
        $this->service->create($dto);
        return Response::makeRedirect('/admin/equipes');
    }

    #[Get('/admin/equipes/{id}/editar', name: 'admin.equipes.edit')]
    public function edit(int $id)
    {
        $equipe = $this->service->findById($id);
        return view('admin/equipes/form', [
            'title' => 'Editar Equipe',
            'equipe' => $equipe
        ]);
    }

    #[Post('/admin/equipes/{id}/update', name: 'admin.equipes.update')]
    public function update(int $id, EquipeDTO $dto)
    {
        $this->service->update($id, $dto);
        return Response::makeRedirect('/admin/equipes');
    }

    #[Post('/admin/equipes/{id}/deletar', name: 'admin.equipes.delete')]
    public function delete(int $id)
    {
        $this->service->delete($id);
        return Response::makeRedirect('/admin/equipes');
    }
}
