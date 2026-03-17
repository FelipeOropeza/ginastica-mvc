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
    #[Get('/admin/atletas', name: 'admin.atletas.index')]
    public function index()
    {
        // Busca atletas com relacionamentos (usuário, equipe, categoria)
        $atletaModel = new Atleta();
        $atletas = $atletaModel->with(['usuario', 'equipe', 'categoria'])->get();

        return view('admin/atletas/index', [
            'title' => 'Gestão de Atletas',
            'atletas' => $atletas
        ]);
    }

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
