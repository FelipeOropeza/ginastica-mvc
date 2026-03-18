<?php

namespace App\Controllers\Atleta;

use App\Models\Atleta;
use App\Models\Equipe;
use App\Models\Categoria;
use Core\Http\Response;
use Core\Attributes\Route\Get;
use Core\Attributes\Route\Post;
use Core\Attributes\Route\Middleware;

#[Middleware(['auth', 'role:atleta'])]
class ProfileController
{
    #[Get('/atleta/perfil', name: 'atleta.profile')]
    public function edit()
    {
        $usuarioId = session('user')['id'];
        $atleta = (new Atleta())->where('usuario_id', '=', $usuarioId)->first();
        
        $equipes = (new Equipe())->where('ativo', '=', 1)->get();
        $categorias = (new Categoria())->all();

        return view('atleta/perfil', [
            'title' => 'Completar Perfil',
            'atleta' => $atleta,
            'equipes' => $equipes,
            'categorias' => $categorias
        ]);
    }

    #[Post('/atleta/perfil/update', name: 'atleta.profile.update')]
    public function update()
    {
        $usuarioId = session('user')['id'];
        $atleta = (new Atleta())->where('usuario_id', '=', $usuarioId)->first();
        
        if (!$atleta) {
            $atleta = new Atleta();
            $atleta->usuario_id = $usuarioId;
        }
        
        $atleta->nome_completo = request()->get('nome_completo');
        $atleta->data_nascimento = request()->get('data_nascimento');
        $atleta->cpf = request()->get('cpf');
        $atleta->equipe_id = request()->get('equipe_id');
        $atleta->categoria_id = request()->get('categoria_id');
        
        $atleta->save();

        return Response::makeRedirect('/atleta/dashboard');
    }
}
