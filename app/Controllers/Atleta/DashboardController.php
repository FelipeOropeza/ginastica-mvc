<?php

namespace App\Controllers\Atleta;

use App\Models\Atleta;
use App\Models\Inscricao;
use App\Models\Resultado;
use Core\Attributes\Route\Get;
use Core\Attributes\Route\Middleware;

#[Middleware(['auth', 'role:atleta'])]
class DashboardController
{
    #[Get('/atleta/dashboard', name: 'atleta.dashboard')]
    public function index()
    {
        $usuarioId = session('user')['id'];
        
        $atleta = (new Atleta())
            ->with(['equipe.treinadores'])
            ->where('usuario_id', '=', $usuarioId)
            ->first();

        if (!$atleta) {
            return view('atleta/dashboard', [
                'title' => 'Meu Painel',
                'atleta' => null,
                'perfilIncompleto' => true,
                'totalInscricoes' => 0,
                'melhorNota' => '--',
                'atividades' => []
            ]);
        }

        // Verifica se o perfil está completo
        $perfilIncompleto = !$atleta->data_nascimento || !$atleta->equipe_id || !$atleta->categoria_id;

        // Inscrições ativas
        $totalInscricoes = (new Inscricao())->where('atleta_id', '=', $atleta->id)->count();

        // Melhor nota
        $resultadoModel = new Resultado();
        $melhorResultado = $resultadoModel->whereIn('inscricao_id', function($q) use ($atleta) {
            $q->select('id')->from('inscricoes')->where('atleta_id', '=', $atleta->id);
        })->orderBy('nota_final', 'DESC')->first();

        // Atividades recentes (Inscrições e notas publicadas)
        $atividades = (new Inscricao())
            ->with(['competicao', 'prova', 'resultado'])
            ->where('atleta_id', '=', $atleta->id)
            ->orderBy('id', 'DESC')
            ->limit(5)
            ->get();

        return view('atleta/dashboard', [
            'title' => 'Meu Painel',
            'atleta' => $atleta,
            'perfilIncompleto' => $perfilIncompleto,
            'totalInscricoes' => $totalInscricoes,
            'melhorNota' => $melhorResultado->nota_final ?? '--',
            'atividades' => $atividades
        ]);
    }
}
