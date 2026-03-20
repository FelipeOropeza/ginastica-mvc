<?php

namespace App\Controllers\Admin;

use App\Models\Atleta;
use App\Models\Equipe;
use App\Models\Categoria;
use Core\Http\Response;
use Core\Attributes\Route\Get;
use Core\Attributes\Route\Post;
use Core\Attributes\Route\Middleware;

#[Middleware(['auth', 'role:admin,operador'])]
class AtletaController
{
    /**
     * Lista todos os atletas com seus relacionamentos.
     */
    #[Get('/admin/atletas', name: 'admin.atletas.index')]
    public function index()
    {
        $atletas = (new Atleta())->with(['usuario', 'equipe', 'categoria'])->get();

        return view('admin/atletas/index', [
            'title' => 'Gestão de Atletas',
            'atletas' => $atletas
        ]);
    }

    /**
     * Exibe o formulário de edição de um atleta.
     */
    #[Get('/admin/atletas/{id}/editar', name: 'admin.atletas.edit')]
    public function edit(int $id)
    {
        $atleta = (new Atleta())->find($id);
        $equipes = (new Equipe())->all();
        $categorias = (new Categoria())->all();

        if (request()->isHtmx()) {
             return view('admin/atletas/partials/modal_edit', [
                'atleta' => $atleta,
                'equipes' => $equipes,
                'categorias' => $categorias
            ]);
        }

        return view('admin/atletas/edit', [
            'title' => 'Editar Atleta',
            'atleta' => $atleta,
            'equipes' => $equipes,
            'categorias' => $categorias
        ]);
    }

    /**
     * Atualiza os dados técnicos do atleta.
     */
    #[Post('/admin/atletas/{id}/update', name: 'admin.atletas.update')]
    public function update(int $id)
    {
        $atleta = (new Atleta())->find($id);
        
        $atleta->nome_completo = request()->get('nome_completo');
        $atleta->cpf = request()->get('cpf');
        $atleta->equipe_id = request()->get('equipe_id');
        $atleta->categoria_id = request()->get('categoria_id');
        $atleta->ativo = request()->get('ativo') ? 1 : 0;
        
        if ($atleta->save()) {
             if (request()->isHtmx()) {
                 return new Response("<script>window.location.reload();</script>");
             }
             return Response::makeRedirect('/admin/atletas');
        }

        return new Response("Erro ao salvar.", 400);
    }

    /**
     * Alterna o status ativo/inativo.
     */
    #[Get('/admin/atletas/{id}/status', name: 'admin.atletas.toggle_status')]
    public function toggleStatus(int $id)
    {
        $atleta = (new Atleta())->find($id);
        if ($atleta) {
            $atleta->ativo = $atleta->ativo ? 0 : 1;
            $atleta->save();
        }

        return Response::makeRedirect('/admin/atletas');
    }
}
