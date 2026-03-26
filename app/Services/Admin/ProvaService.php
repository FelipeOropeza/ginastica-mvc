<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\Prova;
use App\Models\Competicao;
use App\Models\Nota;
use App\Models\Inscricao;
use App\Models\Resultado;
use App\DTOs\Admin\ProvaDTO;
use App\Services\AuditoriaService;
use Core\Exceptions\HttpException;
use Core\Exceptions\ValidationException;

class ProvaService
{
    /**
     * Retorna as provas de uma competição.
     */
    public function getByCompeticao(int $competicaoId)
    {
        return (new Prova())->where('competicao_id', '=', $competicaoId)->get();
    }

    /**
     * Adiciona uma nova prova a uma competição.
     */
    public function create(int $competicaoId, ProvaDTO $dto): Prova
    {
        (new Competicao())->findOrFail($competicaoId);

        $prova = new Prova();
        $prova->competicao_id = $competicaoId;
        $prova->categoria_id = $dto->categoria_id;
        $prova->aparelho = $dto->aparelho;
        $prova->tipo_calculo = $dto->tipo_calculo;
        $prova->num_jurados = $dto->num_jurados;
        $prova->descricao = $dto->descricao;
        $prova->max_participantes = $dto->max_participantes;
        $prova->save();

        return $prova;
    }

    /**
     * Deleta uma prova.
     */
    public function delete(int $id): bool
    {
        $prova = (new Prova())->find($id);

        if ($prova) {
            return $prova->delete($id);
        }

        return false;
    }

    /**
     * Busca uma prova pelo ID.
     */
    public function findById(int $id): Prova
    {
        return (new Prova())->findOrFail($id);
    }

    /**
     * Reabre a nota de um jurado para um atleta específico sem afetar os demais.
     * Encapsula toda a lógica que antes estava no ProvaController (fat controller).
     *
     * Regras:
     * - Bloqueia se a competição estiver encerrada (resultados são definitivos).
     * - Deleta a nota específica do jurado.
     * - Marca inscricao.reaberta = 1 para autorizar re-entrada pelo juiz.
     * - Recalcula o resultado ou o deleta se não houver mais notas.
     * - Recalcula o ranking da prova.
     *
     * @return int ID da prova para redirect
     */
    public function reabrirNota(int $notaId): int
    {
        $nota = (new Nota())->find($notaId);

        if (!$nota) {
            abort(404, 'Nota não encontrada.');
        }

        $inscricaoId = $nota->inscricao_id;
        $inscricao = (new Inscricao())->with(['prova', 'competicao'])->find($inscricaoId);

        if (!$inscricao) {
            abort(404, 'Inscrição não encontrada.');
        }

        // Bloquear se a competição já foi encerrada
        if ($inscricao->competicao && $inscricao->competicao->status === 'encerrada') {
            throw new ValidationException(['status' => 'A competição já foi encerrada. Os resultados são definitivos.']);
        }

        $provaId = $inscricao->prova_id;

        // 1. Registrar auditoria antes de deletar
        AuditoriaService::registrar(
            'nota.reaberta',
            'notas',
            $notaId,
            (array) $nota,
            ['acao' => 'deletada_para_reabrir']
        );

        // 2. Deletar a nota específica
        (new Nota())->delete($notaId);

        // 2. Marcar a inscrição como reaberta (flag explícita para o juiz re-entrar)
        $inscricao->reaberta = 1;
        $inscricao->save();

        // 3. Recalcular ou limpar o resultado
        $notasRestantes = (new Nota())->where('inscricao_id', '=', $inscricaoId)->get();

        if (empty($notasRestantes)) {
            $resultado = (new Resultado())->where('inscricao_id', '=', $inscricaoId)->first();
            if ($resultado) {
                (new Resultado())->delete($resultado->id);
            }
        } else {
            $avaliacaoService = app(\App\Services\Juiz\AvaliacaoService::class);
            $avaliacaoService->atualizarResultadoFinal($inscricaoId);
        }

        // 4. Recalcular ranking da prova
        Resultado::calcularRanking($provaId);

        return $provaId;
    }
    /**
     * Autoriza o lançamento de nota para um atleta, mesmo com prova encerrada.
     * Útil quando um atleta foi pulado ou precisa de nova nota sem deletar a anterior.
     */
    public function autorizarLancamento(int $inscricaoId): int
    {
        $inscricao = (new \App\Models\Inscricao())->with(['prova', 'competicao'])->find($inscricaoId);

        if (!$inscricao) {
            abort(404, 'Inscrição não encontrada.');
        }

        if ($inscricao->competicao && $inscricao->competicao->status === 'encerrada') {
            throw new \Core\Exceptions\ValidationException(['status' => 'A competição já foi encerrada.']);
        }

        $inscricao->reaberta = 1;
        $inscricao->save();

        return $inscricao->prova_id;
    }
}
