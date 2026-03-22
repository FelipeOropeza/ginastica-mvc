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
        $designacoes = (new JuradoDesignacao())
            ->where('usuario_id', '=', $juradoId)
            ->with(['prova'])
            ->get();

        $provas = [];
        foreach ($designacoes as $d) {
            if ($d->prova->competicao_id == $competicaoId) {
                if (!isset($provas[$d->prova->id])) {
                    $provas[$d->prova->id] = [
                        'prova' => $d->prova,
                        'designacao' => $d
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
    public function registrarNota(int $juradoId, int $inscricaoId, float $valor, ?string $observacao = null)
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

        // Impedir nota duplicada para o mesmo jurado/inscrição/critério 
        // mas as notas podem ser por critério. O jurado só tem um critério por designação.
        $jaAvaliado = (new Nota())
            ->where('inscricao_id', '=', $inscricaoId)
            ->where('jurado_id', '=', $juradoId)
            ->where('criterio', '=', $designacao->criterio)
            ->first();

        if ($jaAvaliado) {
            throw new ValidationException(['nota' => "Você já enviou a nota para este atleta nesta categoria."]);
        }

        $nota = new Nota();
        $nota->inscricao_id = $inscricaoId;
        $nota->jurado_id = $juradoId;
        $nota->criterio = $designacao->criterio;
        $nota->valor = $valor;
        $nota->observacao = $observacao;
        $nota->registrado_em = date('Y-m-d H:i:s');

        if ($nota->save()) {
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

        $resultado->nota_d = $notaD;
        $resultado->nota_e = $notaE;
        $resultado->penalidade = $totalPenalidades;
        $resultado->nota_final = max(0, $notaFinal);

        $numVotosTotal = count($votosD) + count($votosE) + count($votosGeral);
        $resultado->calculado = ($numVotosTotal >= ($prova->num_jurados ?? 3));

        if ($resultado->save()) {
            Resultado::calcularRanking($inscricao->prova_id);
        }
    }
}
