<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\Competicao;
use App\Models\Prova;
use App\Models\Inscricao;
use App\Models\Resultado;
use App\Models\Atleta;

class RelatorioService
{
    public function getCompetitions(): array
    {
        return (new Competicao())->with('provas')->get();
    }

    public function getCompetitionsWithResults(): array
    {
        $competitions = $this->getCompetitions();
        
        foreach ($competitions as $competition) {
            $competition->total_inscritos = (new Inscricao())
                ->where('competicao_id', '=', $competition->id)
                ->count();
            
            $competition->total_resultados = (new Resultado())
                ->join('inscricoes', 'inscricoes.id = resultados.inscricao_id')
                ->where('inscricoes.competicao_id', '=', $competition->id)
                ->count();
        }
        
        return $competitions;
    }

    public function getCompetitionDetails(int $competitionId): ?Competicao
    {
        $competition = (new Competicao())->find($competitionId);
        
        if (!$competition) {
            return null;
        }

        $competition->provas = (new Prova())
            ->where('competicao_id', '=', $competitionId)
            ->with(['categoria', 'inscricoes' => function($query) {
                $query->with(['atleta', 'resultado'])->orderBy('ordem_apresentacao');
            }])
            ->get();

        return $competition;
    }

    public function getResultsByProva(int $provaId): array
    {
        $prova = (new Prova())->find($provaId);
        
        if (!$prova) {
            return [];
        }

        $inscricoes = (new Inscricao())
            ->where('prova_id', '=', $provaId)
            ->with(['atleta.equipe', 'resultado', 'notas'])
            ->orderBy('ordem_apresentacao')
            ->get();

        $results = [];
        foreach ($inscricoes as $inscricao) {
            if ($inscricao->resultado) {
                $results[] = [
                    'inscricao' => $inscricao,
                    'atleta' => $inscricao->atleta,
                    'equipe' => $inscricao->atleta?->equipe,
                    'resultado' => $inscricao->resultado,
                    'notas' => $inscricao->notas,
                    'classificacao' => $inscricao->resultado->classificacao,
                    'nota_final' => $inscricao->resultado->nota_final,
                    'podio' => $inscricao->resultado->podio,
                ];
            }
        }

        usort($results, fn($a, $b) => $a['classificacao'] <=> $b['classificacao']);

        return $results;
    }

    public function exportToCsv(int $competitionId): ?string
    {
        $competition = (new Competicao())->find($competitionId);
        
        if (!$competition) {
            return null;
        }

        $provas = (new Prova())
            ->where('competicao_id', '=', $competitionId)
            ->get();

        $csv = [];
        $csv[] = ["Relatório de Resultados - {$competition->nome}"];
        $csv[] = ["Data: {$competition->data_inicio} a {$competition->data_fim}"];
        $csv[] = ["Local: {$competition->local}"];
        $csv[] = [];

        foreach ($provas as $prova) {
            $csv[] = ["Prova: {$prova->aparelho}"];
            
            if ($prova->tipo_calculo === 'nota_d_mais_e') {
                $csv[] = ["Classificação", "Atleta", "Equipe", "Nota D", "Nota E", "Penalidade", "Nota Final", "Podio"];
            } else {
                $csv[] = ["Classificação", "Atleta", "Equipe", "Média", "Penalidade", "Nota Final", "Podio"];
            }

            $results = $this->getResultsByProva($prova->id);
            
            foreach ($results as $row) {
                if ($prova->tipo_calculo === 'nota_d_mais_e') {
                    $csv[] = [
                        $row['classificacao'] ?? '-',
                        $row['atleta']?->nome_completo ?? '-',
                        $row['equipe']?->nome ?? '-',
                        $row['resultado']->nota_d ?? '-',
                        $row['resultado']->nota_e ?? '-',
                        $row['resultado']->penalidade ?? '0',
                        $row['nota_final'] ?? '-',
                        $row['podio'] ?? '-',
                    ];
                } else {
                    $csv[] = [
                        $row['classificacao'] ?? '-',
                        $row['atleta']?->nome_completo ?? '-',
                        $row['equipe']?->nome ?? '-',
                        $row['resultado']->nota_d ?? '-', // Aqui nota_d já tem a média salva
                        $row['resultado']->penalidade ?? '0',
                        $row['nota_final'] ?? '-',
                        $row['podio'] ?? '-',
                    ];
                }
            }
            
            $csv[] = [];
        }

        $output = '';
        foreach ($csv as $line) {
            $output .= implode(';', $line) . "\n";
        }

        return $output;
    }

    public function getAtletaHistorico(int $atletaId): ?Atleta
    {
        $atleta = (new Atleta())->find($atletaId);
        
        if (!$atleta) {
            return null;
        }

        $atleta->inscricoes = (new Inscricao())
            ->where('atleta_id', '=', $atletaId)
            ->with(['competicao', 'prova', 'resultado'])
            ->orderBy('inscrito_em', 'DESC')
            ->get();

        return $atleta;
    }
}
