<?php

namespace App\Services\Admin;

use App\Models\Competicao;
use App\Models\Prova;
use App\Models\JuradoDesignacao;
use App\DTOs\Admin\CompetitionDTO;
use Core\Exceptions\HttpException;
use Core\Exceptions\ValidationException;

class CompetitionService
{
    /**
     * Retorna todas as competições com suas provas (Eager Loading).
     */
    public function getAll()
    {
        return (new Competicao())->with('provas')->get();
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
        
        if (isset($dto->status) && $dto->status === 'aberta' && $competicao->status !== 'aberta') {
            $this->validateReadiness($competicao);
        }

        if (isset($dto->status)) {
            $competicao->status = $dto->status;
        }

        $competicao->save();

        return $competicao;
    }

    /**
     * Valida se a competição está pronta para abrir inscrições.
     */
    protected function validateReadiness(Competicao $competicao): void
    {
        $provas = (new Prova())->where('competicao_id', '=', $competicao->id)->get();

        if (empty($provas)) {
            throw new ValidationException(['status' => 'A competição precisa ter pelo menos uma prova/aparelho cadastrado antes de ser aberta.']);
        }

        foreach ($provas as $prova) {
            $required = $prova->num_jurados ?? 0;
            if ($required > 0) {
                // Aqui poderíamos ter um count() no QueryBuilder, mas vamos usar a relação se disponível ou o modelo
                $count = (new JuradoDesignacao())->where('prova_id', '=', $prova->id)->count();
                
                if ($count < $required) {
                    $aparelho = str_replace('_', ' ', $prova->aparelho);
                    throw new ValidationException(['status' => "A prova de {$aparelho} exige {$required} jurado(s), mas apenas {$count} foram designados."]);
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

        if ($status === 'aberta' && $competicao->status !== 'aberta') {
            $this->validateReadiness($competicao);
        }

        $competicao->status = $status;
        $competicao->save();

        return $competicao;
    }

    /**
     * Deleta uma competição.
     */
    public function delete(int $id): bool
    {
        $competicao = (new Competicao())->find($id);
        
        if ($competicao) {
            return $competicao->delete($id);
        }

        return false;
    }
}
