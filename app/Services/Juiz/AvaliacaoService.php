<?php

namespace App\Services\Juiz;

use App\Models\Nota;
use App\Models\Inscricao;
use App\Models\Competicao;
use App\Models\Prova;
use App\Models\JuradoDesignacao;
use App\Models\Resultado;
use Core\Exceptions\ValidationException;
use Core\Exceptions\HttpException;

class AvaliacaoService
{
    public function __construct(protected \Symfony\Component\Mercure\HubInterface $hub)
    {
    }

    /**
     * Obtém as competições onde o jurado tem designações.
     */
    public function getCompeticoesDoJurado(int $juradoId)
    {
        // Encontra as provas designadas ao jurado
        $designacoes = (new JuradoDesignacao())
            ->where('usuario_id', '=', $juradoId)
            ->with(['prova.competicao', 'prova.categoria'])
            ->get();

        $competicoes = [];
        foreach ($designacoes as $d) {
            $comp = $d->prova->competicao;
            if (!$comp) continue;
            
            if (!isset($competicoes[$comp->id])) {
                // Preparamos a lista de provas do jurado dentro da competição
                $comp->minhas_provas = [];
                $competicoes[$comp->id] = $comp;
            }
            
            // Atribuímos a prova e a designação diretamente ao objeto competição
            $competicoes[$comp->id]->minhas_provas[] = $d;
        }

        return array_values($competicoes);
    }

    /**
     * Obtém as provas de uma competição designadas ao jurado.
     */
    public function getProvasDesignadas(int $juradoId, int $competicaoId)
    {
        // Filtra diretamente no SQL via JOIN (antes filtrava em PHP — ineficiente)
        $db = \Core\Database\Connection::getInstance();
        $stmt = $db->prepare("
            SELECT d.*, p.id AS prova_id_ref
            FROM designacoes_jurados d
            JOIN provas p ON p.id = d.prova_id
            WHERE d.usuario_id = :jurado_id
              AND p.competicao_id = :competicao_id
        ");
        $stmt->execute(['jurado_id' => $juradoId, 'competicao_id' => $competicaoId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $provas = [];
        foreach ($rows as $row) {
            $provaId = $row['prova_id'];
            if (!isset($provas[$provaId])) {
                $designacao = (new JuradoDesignacao())->find($row['id']);
                if ($designacao) {
                    // Carrega a prova relacionada
                    $designacao->prova = (new Prova())->find($provaId);
                    $provas[$provaId] = [
                        'prova'      => $designacao->prova,
                        'designacao' => $designacao,
                    ];
                }
            }
        }

        return array_values($provas);
    }

    /**
     * Identifica o atleta atual que deve ser avaliado na prova de forma sincronizada.
     * Segue a ordem de apresentação e só avança quando o resultado do anterior estiver 'calculado'.
     */
    public function getAtletaAtivo(int $provaId)
    {
        $inscricoes = (new Inscricao())
            ->where('prova_id', '=', $provaId)
            ->whereIn('status', ['confirmada', 'pendente'])
            ->orderBy('ordem_apresentacao', 'ASC') // Ordem nula vai pro final no SQL dependendo do Driver, mas preferencialmente ordenamos por ID como fallback
            ->orderBy('id', 'ASC')
            ->get();

        foreach ($inscricoes as $ins) {
            $resultado = (new Resultado())->where('inscricao_id', '=', $ins->id)->first();
            
            // Se o resultado não existe ou não está calculado, este é o atleta atual (bloqueando a fila)
            if (!$resultado || !$resultado->calculado) {
                return $ins;
            }
        }

        return null; // Todos os atletas já foram completamente avaliados
    }

    /**
     * Registra a nota para um atleta.
     */
    public function registrarNota(int $juradoId, int $inscricaoId, float $valor, ?string $observacao = null, ?string $criterio = null)
    {
        $inscricao = (new Inscricao())->with(['competicao', 'prova'])->where('id', '=', $inscricaoId)->first();

        if (!$inscricao) {
            throw new HttpException("Inscrição não encontrada.", 404);
        }

        $competicao = $inscricao->competicao;

        // Regra de status da competição
        if ($competicao->status !== 'em_andamento') {
            $msg = $competicao->status === 'encerrada'
                ? "A competição já foi encerrada. Não é possível mais lançar notas."
                : "A competição ainda não foi iniciada (em andamento) para receber notas.";
            throw new ValidationException(['status' => $msg]);
        }

        // Verificar designação do jurado
        $designacao = (new JuradoDesignacao())
            ->where('usuario_id', '=', $juradoId)
            ->where('prova_id', '=', $inscricao->prova_id)
            ->first();

        if (!$designacao) {
            throw new HttpException("Você não tem permissão para avaliar esta prova.", 403);
        }

        // Usamos o critério passado ou o da designação
        $criterioFinal = $criterio ?: $designacao->criterio;

        // Impedir nota duplicada para o mesmo jurado/inscrição/critério 
        $jaAvaliado = (new Nota())
            ->where('inscricao_id', '=', $inscricaoId)
            ->where('jurado_id', '=', $juradoId)
            ->where('criterio', '=', $criterioFinal)
            ->first();

        if ($jaAvaliado) {
            throw new ValidationException(['nota' => "Você já enviou a nota para este atleta neste critério ($criterioFinal)."]);
        }

        // Regra de prova encerrada: bloqueia NOVAS notas, mas permite re-entrada
        // Re-entrada é autorizada se o admin explicitamente reabriu a inscrição (reaberta=1)
        $prova = $inscricao->prova;
        if ($prova && $prova->encerrada) {
            $isReabertura = (bool) ($inscricao->reaberta ?? false);

            if (!$isReabertura) {
                throw new ValidationException(['status' => "Esta prova (" . str_replace('_', ' ', $prova->aparelho) . ") já foi encerrada."]);
            }
        }

        $nota = new Nota();
        $nota->inscricao_id = $inscricaoId;
        $nota->jurado_id = $juradoId;
        $nota->criterio = $criterioFinal;
        $nota->valor = $valor;
        $nota->observacao = $observacao;
        $nota->registrado_em = date('Y-m-d H:i:s');

        if ($nota->save()) {
            // Fecha a janela de reaberta após o juiz submeter a nota
            if ($inscricao->reaberta) {
                $inscricao->reaberta = 0;
                $inscricao->save();
            }
            $this->atualizarResultadoFinal($inscricaoId);

            return true;
        }

        return false;
    }

    /**
     * Recalcula a nota final do atleta e atualiza na tabela resultados.
     */
    public function atualizarResultadoFinal(int $inscricaoId)
    {
        $inscricao = (new Inscricao())->with(['prova'])->where('id', '=', $inscricaoId)->first();
        if (!$inscricao) return;

        $prova = $inscricao->prova;
        $notas = (new Nota())->where('inscricao_id', '=', $inscricaoId)->get();

        $votosD = [];
        $votosE = [];
        $votosGeral = [];
        $penalidades = [];

        foreach ($notas as $n) {
            if ($n->criterio === 'nota_d') {
                $votosD[] = (float) $n->valor;
            } elseif ($n->criterio === 'nota_e') {
                $votosE[] = (float) $n->valor;
            } elseif ($n->criterio === 'geral') {
                $votosGeral[] = (float) $n->valor;
            } elseif (in_array($n->criterio, ['penalidade', 'arbitro_superior'])) {
                $penalidades[] = (float) $n->valor;
            }
        }

        // --- CÁLCULO DA BASE ---
        $notaD = !empty($votosD) ? array_sum($votosD) / count($votosD) : 0;
        $notaE = 0;
        $notaGeral = 0;

        if ($prova->tipo_calculo === 'media_sem_extremos') {
            // Regra: Descarta maior e menor antes da média
            if (!empty($votosE) && count($votosE) >= 3) {
                sort($votosE);
                array_shift($votosE); // remove menor
                array_pop($votosE);   // remove maior
                $notaE = array_sum($votosE) / count($votosE);
            } elseif (!empty($votosE)) {
                $notaE = array_sum($votosE) / count($votosE);
            }

            if (!empty($votosGeral) && count($votosGeral) >= 3) {
                sort($votosGeral);
                array_shift($votosGeral);
                array_pop($votosGeral);
                $notaGeral = array_sum($votosGeral) / count($votosGeral);
            } elseif (!empty($votosGeral)) {
                $notaGeral = array_sum($votosGeral) / count($votosGeral);
            }
        } else {
            // Média Simples (e fallback para FIG se não houver juízes suficientes para sem extremos)
            if (!empty($votosE)) $notaE = array_sum($votosE) / count($votosE);
            if (!empty($votosGeral)) $notaGeral = array_sum($votosGeral) / count($votosGeral);
        }

        $totalPenalidades = array_sum($penalidades);
        $notaFinal = 0;

        // Se houver votos GERAIS (Juiz único ou banca unificada), eles têm precedência no cálculo final
        if (!empty($votosGeral)) {
            $notaFinal = $notaGeral - $totalPenalidades;
        } else {
            // Caso contrário, usamos a soma de D + E (Seja FIG ou Média Simples de D e E)
            $notaFinal = ($notaD + $notaE) - $totalPenalidades;
        }

        $resultado = (new Resultado())->where('inscricao_id', '=', $inscricaoId)->first();
        if (!$resultado) {
            $resultado = new Resultado();
            $resultado->inscricao_id = $inscricaoId;
        }

        $resultado->nota_d = !empty($votosGeral) ? $notaGeral : $notaD;
        $resultado->nota_e = $notaE;
        $resultado->penalidade = $totalPenalidades;
        $resultado->nota_final = max(0, $notaFinal);

        $numVotosTotal = count($votosD) + count($votosE) + count($votosGeral);
        $resultado->calculado = (int) ($numVotosTotal >= ($prova->num_jurados ?? 3));

        if ($resultado->save()) {
            Resultado::calcularRanking($inscricao->prova_id);

            // Auto-encerrar prova se todos os atletas foram avaliados
            $this->verificarAutoEncerramento($inscricao->prova_id);

            // Transmitir evento Mercure para o dashboard AO VIVO
            $update = new \Symfony\Component\Mercure\Update(
                'competicao-' . $inscricao->competicao_id,
                json_encode([
                    'type' => 'score_update',
                    'prova_id' => $inscricao->prova_id,
                    'atleta' => $inscricao->atleta->nome_completo ?? 'Atleta',
                    'nota_final' => number_format($resultado->nota_final, 3)
                ])
            );
            $this->hub->publish($update);
        }
    }

    /**
     * Verifica se todas as inscrições de uma prova já foram totalmente avaliadas.
     * Se sim, encerra a prova automaticamente.
     */
    private function verificarAutoEncerramento(int $provaId): void
    {
        $inscricoes = (new Inscricao())
            ->where('prova_id', '=', $provaId)
            ->whereIn('status', ['confirmada', 'pendente'])
            ->get();

        if (empty($inscricoes)) return;

        // Busca TODOS os resultados da prova em uma única query (antes era 1 query por atleta = N+1)
        $inscricaoIds = array_map(fn($i) => $i->id, $inscricoes);
        $resultados = (new Resultado())
            ->whereIn('inscricao_id', $inscricaoIds)
            ->get();

        // Indexa por inscricao_id para lookup O(1)
        $resultadoMap = [];
        foreach ($resultados as $r) {
            $resultadoMap[$r->inscricao_id] = $r;
        }

        // Verifica se todos têm resultado calculado
        foreach ($inscricoes as $ins) {
            $resultado = $resultadoMap[$ins->id] ?? null;
            if (!$resultado || !$resultado->calculado) {
                return; // Ainda tem atleta pendente
            }
        }

        // Todos calculados — encerrar prova automaticamente
        $prova = (new Prova())->find($provaId);
        if ($prova && !$prova->encerrada) {
            $prova->encerrada = 1;
            $prova->save();
        }
    }
}
