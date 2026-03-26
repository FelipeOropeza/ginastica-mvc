<?php

namespace App\Services\Admin;

use App\Models\Competicao;
use App\Models\Prova;
use App\Models\JuradoDesignacao;
use App\DTOs\Admin\CompetitionDTO;
use App\Services\AuditoriaService;
use Core\Exceptions\HttpException;
use Core\Exceptions\ValidationException;

class CompetitionService
{
    /**
     * Retorna todas as competições com suas provas (Eager Loading).
     */
    public function getAll()
    {
        return (new Competicao())->with('provas')->withTrashed()->get();
    }

    /**
     * Busca uma competição pelo ID ou lança 404.
     */
    public function findById(int $id): Competicao
    {
        return (new Competicao())->findOrFail($id);
    }

    /**
     * Cria uma nova competição.
     */
    public function create(CompetitionDTO $dto): Competicao
    {
        $competicao = new Competicao();
        $competicao->nome = $dto->nome;
        $competicao->data_inicio = $dto->data_inicio;
        $competicao->data_fim = $dto->data_fim;
        $competicao->local = $dto->local;
        $competicao->descricao = $dto->descricao;
        $competicao->status = $dto->status ?? 'rascunho';
        $competicao->criado_por = session()->get('user')['id'];
        $competicao->save();

        return $competicao;
    }

    /**
     * Atualiza uma competição existente.
     */
    public function update(int $id, CompetitionDTO $dto): Competicao
    {
        $competicao = $this->findById($id);

        $competicao->nome = $dto->nome;
        $competicao->data_inicio = $dto->data_inicio;
        $competicao->data_fim = $dto->data_fim;
        $competicao->local = $dto->local;
        $competicao->descricao = $dto->descricao;
        
        if (isset($dto->status) && in_array($dto->status, ['aberta', 'em_andamento']) && $competicao->status !== $dto->status) {
            $this->validateReadiness($competicao, $dto->status);
        }

        if (isset($dto->status)) {
            $competicao->status = $dto->status;
        }

        $competicao->save();

        return $competicao;
    }

    /**
     * Valida se a competição está pronta para abrir inscrições ou para ser ativada.
     */
    protected function validateReadiness(Competicao $competicao, string $status): void
    {
        $provas = (new Prova())->where('competicao_id', '=', $competicao->id)->get();

        if (empty($provas)) {
            throw new ValidationException(['status' => 'A competição precisa ter pelo menos uma prova/aparelho cadastrado.']);
        }

        // Se estiver tentando ATIVAR a competição (em_andamento)
        if ($status === 'em_andamento') {
            $inscricoes = (new \App\Models\Inscricao())
                ->where('competicao_id', '=', $competicao->id)
                ->where('status', '=', 'confirmada')
                ->get();

            if (empty($inscricoes)) {
                throw new ValidationException(['status' => 'Não é possível ativar uma competição sem atletas inscritos e confirmados.']);
            }

            foreach ($inscricoes as $ins) {
                if (empty($ins->ordem_apresentacao)) {
                    throw new ValidationException(['status' => 'A ordem de apresentação dos atletas ainda não foi gerada para esta competição.']);
                }
            }
        }

        // Valida se as provas têm juízes designados e limite de participantes
        foreach ($provas as $prova) {
            $required = $prova->num_jurados ?? 0;
            if ($required > 0) {
                $count = (new JuradoDesignacao())->where('prova_id', '=', $prova->id)->count();

                if ($count < $required) {
                    $aparelho = str_replace('_', ' ', $prova->aparelho);
                    throw new ValidationException(['status' => "A prova de {$aparelho} exige {$required} jurado(s), mas apenas {$count} foram designados."]);
                }
            }

            // Valida limite máximo de participantes por prova (campo antes ignorado)
            if ($status === 'em_andamento' && $prova->max_participantes > 0) {
                $totalInscritos = (new \App\Models\Inscricao())
                    ->where('prova_id', '=', $prova->id)
                    ->where('status', '=', 'confirmada')
                    ->count();

                if ($totalInscritos > $prova->max_participantes) {
                    $aparelho = str_replace('_', ' ', $prova->aparelho);
                    throw new ValidationException(['status' => "A prova de {$aparelho} excede o limite de {$prova->max_participantes} participantes ({$totalInscritos} inscritos)."]);
                }
            }
        }
    }

    /**
     * Atualiza apenas o status da competição com validação.
     */
    public function updateStatus(int $id, string $status): Competicao
    {
        $competicao = $this->findById($id);

        // Bloqueia se tentar ir para um status restrito sem validação
        if (in_array($status, ['aberta', 'em_andamento']) && $competicao->status !== $status) {
            $this->validateReadiness($competicao, $status);
        }

        $statusAnterior = $competicao->status;
        $competicao->status = $status;
        $competicao->save();

        AuditoriaService::registrar(
            'competicao.status_alterado',
            'competicoes',
            $id,
            ['status' => $statusAnterior],
            ['status' => $status]
        );

        return $competicao;
    }

    /**
     * Sorteia a ordem de apresentação de todos os atletas em todas as provas da competição.
     */
    public function shuffleAll(int $competitionId): void
    {
        $provas = (new Prova())->where('competicao_id', '=', $competitionId)->get();
        foreach ($provas as $prova) {
            $inscricoes = (new \App\Models\Inscricao())
                ->where('prova_id', '=', $prova->id)
                ->where('status', '=', 'confirmada')
                ->get();

            if (!empty($inscricoes)) {
                shuffle($inscricoes);
                foreach ($inscricoes as $i => $ins) {
                    $ins->ordem_apresentacao = $i + 1;
                    $ins->save();
                }
            }
        }
    }

    /**
     * Deleta uma competição.
     */
    public function delete(int $id): bool
    {
        $competicao = (new Competicao())->find($id);
        
        if ($competicao) {
            AuditoriaService::registrar('competicao.deletada', 'competicoes', $id, (array) $competicao);
            return $competicao->delete($id);
        }

        return false;
    }
}
