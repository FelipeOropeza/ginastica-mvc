<?php

namespace App\Services\Admin;

use App\Models\Competicao;
use App\Models\Atleta;

class DashboardService
{
    public function getStats(): array
    {
        return [
            'total_competicoes' => (new Competicao())->count(),
            'competicoes_ativas' => (new Competicao())->where('status', 'aberta')->count(),
            'total_atletas' => (new Atleta())->count(),
            'proximas_competicoes' => (new Competicao())
                ->where('status', '!=', 'encerrada')
                ->orderBy('data_inicio', 'ASC')
                ->limit(5)
                ->get(),
        ];
    }
}
