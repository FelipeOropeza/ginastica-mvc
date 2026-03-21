<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Services\Admin\RelatorioService;
use Core\Http\Response;
use Core\Attributes\Route\Get;
use Core\Attributes\Route\Post;
use Core\Attributes\Route\Middleware;

#[Middleware(['auth', 'role:admin,operador'])]
class RelatorioController
{
    public function __construct(private RelatorioService $service)
    {
    }

    #[Get('/admin/relatorios', name: 'admin.relatorios.index')]
    public function index()
    {
        $competitions = $this->service->getCompetitionsWithResults();

        return view('admin/relatorios/index', [
            'title' => 'Relatórios',
            'competitions' => $competitions
        ]);
    }

    #[Get('/admin/relatorios/competicao/{id}', name: 'admin.relatorios.competicao')]
    public function competicao(int $id)
    {
        $competition = $this->service->getCompetitionDetails($id);

        if (!$competition) {
            abort(404, 'Competição não encontrada.');
        }

        return view('admin/relatorios/competicao', [
            'title' => "Relatório - {$competition->nome}",
            'competition' => $competition
        ]);
    }

    #[Get('/admin/relatorios/competicao/{id}/csv', name: 'admin.relatorios.competicao.csv')]
    public function exportCsv(int $id)
    {
        $csv = $this->service->exportToCsv($id);

        if (!$csv) {
            abort(404, 'Competição não encontrada.');
        }

        $competition = (new \App\Models\Competicao())->find($id);
        $filename = "relatorio_{$competition->nome}_{$competition->id}.csv";
        $filename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $filename);

        return new Response($csv, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    #[Get('/admin/relatorios/atleta/{id}', name: 'admin.relatorios.atleta')]
    public function atleta(int $id)
    {
        $atleta = $this->service->getAtletaHistorico($id);

        if (!$atleta) {
            abort(404, 'Atleta não encontrado.');
        }

        return view('admin/relatorios/atleta', [
            'title' => "Histórico - {$atleta->nome_completo}",
            'atleta' => $atleta
        ]);
    }
}
