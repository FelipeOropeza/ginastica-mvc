<?php

namespace App\Controllers\Atleta;

use App\Models\Atleta;
use Core\Attributes\Route\Get;
use Core\Attributes\Route\Middleware;

#[Middleware(['auth', 'role:atleta'])]
class DashboardController
{
    #[Get('/atleta/dashboard', name: 'atleta.dashboard')]
    public function index()
    {
        $userId = session()->get('user.id');
        $atleta = (new Atleta())->where('usuario_id', '=', $userId)->first();

        // Verifica se o perfil está completo
        $perfilIncompleto = !$atleta || !$atleta->data_nascimento || !$atleta->equipe_id;

        return view('atleta/dashboard', [
            'title' => 'Meu Painel',
            'atleta' => $atleta,
            'perfilIncompleto' => $perfilIncompleto
        ]);
    }
}
