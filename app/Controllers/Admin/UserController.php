<?php

namespace App\Controllers\Admin;

use App\Services\Admin\UserService;
use App\DTOs\Admin\UserDTO;
use Core\Http\Response;
use Core\Attributes\Route\Get;
use Core\Attributes\Route\Post;
use Core\Attributes\Route\Middleware;

#[Middleware(['auth', 'role:admin'])]
class UserController
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    #[Get('/admin/usuarios', name: 'admin.usuarios.index')]
    public function index()
    {
        $role = request()->get('role');
        $usuarios = $this->service->getAll($role);
        $title = $role ? 'Gestão de ' . ucfirst($role) . 's' : 'Gestão de Usuários';
        
        return view('admin/usuarios/index', [
            'title' => $title,
            'usuarios' => $usuarios,
            'currentRole' => $role
        ]);
    }

    #[Get('/admin/usuarios/criar', name: 'admin.usuarios.create')]
    public function create()
    {
        $roles = $this->service->getRoles();
        $equipes = (new \App\Models\Equipe())->all();
        $categorias = (new \App\Models\Categoria())->all();

        return view('admin/usuarios/form', [
            'title' => 'Novo Usuário',
            'roles' => $roles,
            'equipes' => $equipes,
            'categorias' => $categorias
        ]);
    }

    #[Post('/admin/usuarios/store', name: 'admin.usuarios.store')]
    public function store(UserDTO $dto)
    {
        $this->service->create($dto);
        return Response::makeRedirect('/admin/usuarios');
    }

    #[Get('/admin/usuarios/{id}/editar', name: 'admin.usuarios.edit')]
    public function edit(int $id)
    {
        $usuario = $this->service->findById($id);
        $roles = $this->service->getRoles();
        $equipes = (new \App\Models\Equipe())->all();
        $categorias = (new \App\Models\Categoria())->all();

        return view('admin/usuarios/form', [
            'title' => 'Editar Usuário',
            'usuario' => $usuario,
            'roles' => $roles,
            'equipes' => $equipes,
            'categorias' => $categorias
        ]);
    }

    #[Post('/admin/usuarios/{id}/update', name: 'admin.usuarios.update')]
    public function update(int $id, UserDTO $dto)
    {
        $this->service->update($id, $dto);

        return Response::makeRedirect('/admin/usuarios');
    }

    #[Post('/admin/usuarios/{id}/toggle-status', name: 'admin.usuarios.toggle')]
    public function toggleStatus(int $id)
    {
        $this->service->toggleStatus($id);

        if (request()->isHtmx()) {
            return new Response(''); 
        }

        return Response::makeRedirect('/admin/usuarios');
    }

    #[Post('/admin/usuarios/{id}/deletar', name: 'admin.usuarios.delete')]
    public function delete(int $id)
    {
        $this->service->delete($id);

        if (request()->isHtmx()) {
            return new Response('');
        }

        return Response::makeRedirect('/admin/usuarios');
    }
}
