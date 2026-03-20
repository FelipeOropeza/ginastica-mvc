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
            'competicoes_ativas' => (new Competicao())->where('status', 'em_andamento')->count(),
            'total_atletas' => (new \App\Models\Atleta())->count(),
            'total_jurados' => (new \App\Models\Usuario())->where('role_id', '3')->count(), // Assumindo role_id 3 = juiz
            'total_equipes' => (new \App\Models\Equipe())->count(),
            'proximas_competicoes' => (new Competicao())
                ->where('status', '!=', 'encerrada')
                ->orderBy('data_inicio', 'ASC')
                ->limit(5)
                ->get(),
        ];
    }
}
