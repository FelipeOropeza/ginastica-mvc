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

        // Uma única query com GROUP BY ao invés de 2 queries por competição (N+1)
        $rows = \Core\Database\Connection::getInstance()->query("
            SELECT
                i.competicao_id,
                COUNT(DISTINCT i.id)  AS total_inscritos,
                COUNT(DISTINCT r.id)  AS total_resultados
            FROM inscricoes i
            LEFT JOIN resultados r ON r.inscricao_id = i.id
            WHERE i.deleted_at IS NULL
              AND (r.id IS NULL OR r.deleted_at IS NULL)
            GROUP BY i.competicao_id
        ")->fetchAll(\PDO::FETCH_ASSOC);

        $countMap = [];
        foreach ($rows as $row) {
            $countMap[$row['competicao_id']] = [
                'total_inscritos'  => (int) $row['total_inscritos'],
                'total_resultados' => (int) $row['total_resultados'],
            ];
        }

        foreach ($competitions as $competition) {
            $competition->total_inscritos  = $countMap[$competition->id]['total_inscritos']  ?? 0;
            $competition->total_resultados = $countMap[$competition->id]['total_resultados'] ?? 0;
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
                    $mediaDisplay = 0;
                    if (isset($row['resultado'])) {
                        $mediaDisplay = ($row['resultado']->nota_final ?? 0) + ($row['resultado']->penalidade ?? 0);
                    }
                    $csv[] = [
                        $row['classificacao'] ?? '-',
                        $row['atleta']?->nome_completo ?? '-',
                        $row['equipe']?->nome ?? '-',
                        $mediaDisplay,
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
