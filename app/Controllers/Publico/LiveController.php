<?php

declare(strict_types=1);

namespace App\Controllers\Publico;

use App\Models\Competicao;
use App\Models\Prova;
use App\Models\Inscricao;
use App\Models\Resultado;
use Core\Http\Response;
use Core\Attributes\Route\Get;

class LiveController
{
    #[Get('/live/{id}', name: 'publico.live')]
    public function index(int $id)
    {
        $competicao = (new Competicao())->findOrFail($id);
        
        if ($competicao->status !== 'em_andamento') {
            return Response::makeRedirect('/');
        }

        $provas = (new Prova())
            ->where('competicao_id', '=', $id)
            ->with(['inscricoes' => function($db) {
                return $db->whereIn('status', ['confirmada', 'pendente']);
            }])
            ->get();

        // Filtrar provas que têm pelo menos um inscrito
        $provas = array_filter($provas, fn($p) => count($p->inscricoes) > 0);

        return view('publico/live/index', [
            'title' => 'Ao Vivo: ' . $competicao->nome,
            'competicao' => $competicao,
            'provas' => array_values($provas)
        ]);
    }

    #[Get('/live/{id}/prova/{prova_id}', name: 'publico.live.prova')]
    public function prova(int $id, int $prova_id)
    {
        $prova = (new Prova())
            ->where('id', '=', $prova_id)
            ->where('competicao_id', '=', $id)
            ->first();

        if (!$prova) return new Response('Prova não encontrada.', 404);

        $inscricoes = (new Inscricao())
            ->where('prova_id', '=', $prova_id)
            ->whereIn('status', ['confirmada', 'pendente'])
            ->with(['atleta.equipe', 'resultado'])
            ->get();

        // Ordenar por classificação (se houver) ou por nota final descrecente
        usort($inscricoes, function($a, $b) {
            $notaA = $a->resultado->nota_final ?? -1;
            $notaB = $b->resultado->nota_final ?? -1;
            return $notaB <=> $notaA;
        });

        return view('publico/live/partials/prova_table', [
            'prova' => $prova,
            'inscricoes' => $inscricoes
        ]);
    }
}
