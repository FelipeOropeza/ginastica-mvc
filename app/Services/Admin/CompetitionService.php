<?php

namespace App\Services\Admin;

use App\Models\Competicao;
use App\DTOs\Admin\CompetitionDTO;
use Core\Exceptions\HttpException;

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
        
        if (isset($dto->status)) {
            $competicao->status = $dto->status;
        }

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
