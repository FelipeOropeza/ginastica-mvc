<?php

namespace App\Controllers\Admin;

use App\Services\Admin\CategoriaService;
use App\DTOs\Admin\CategoriaDTO;
use Core\Http\Response;
use Core\Attributes\Route\Get;
use Core\Attributes\Route\Post;
use Core\Attributes\Route\Middleware;

#[Middleware(['auth', 'role:admin,operador'])]
class CategoriaController
{
    protected CategoriaService $service;

    public function __construct(CategoriaService $service)
    {
        $this->service = $service;
    }

    #[Get('/admin/categorias', name: 'admin.categorias.index')]
    public function index()
    {
        $categorias = $this->service->getAll();
        return view('admin/categorias/index', [
            'title' => 'Gestão de Categorias',
            'categorias' => $categorias
        ]);
    }

    #[Get('/admin/categorias/criar', name: 'admin.categorias.create')]
    public function create()
    {
        return view('admin/categorias/form', [
            'title' => 'Nova Categoria'
        ]);
    }

    #[Post('/admin/categorias/store', name: 'admin.categorias.store')]
    public function store(CategoriaDTO $dto)
    {
        $this->service->create($dto);
        return Response::makeRedirect('/admin/categorias');
    }

    #[Get('/admin/categorias/{id}/editar', name: 'admin.categorias.edit')]
    public function edit(int $id)
    {
        $categoria = $this->service->findById($id);
        return view('admin/categorias/form', [
            'title' => 'Editar Categoria',
            'categoria' => $categoria
        ]);
    }

    #[Post('/admin/categorias/{id}/update', name: 'admin.categorias.update')]
    public function update(int $id, CategoriaDTO $dto)
    {
        $this->service->update($id, $dto);
        return Response::makeRedirect('/admin/categorias');
    }

    #[Post('/admin/categorias/{id}/deletar', name: 'admin.categorias.delete')]
    public function delete(int $id)
    {
        $this->service->delete($id);
        return Response::makeRedirect('/admin/categorias');
    }
}
