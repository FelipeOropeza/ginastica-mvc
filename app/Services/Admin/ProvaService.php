<?php

namespace App\Services\Admin;

use App\Models\Prova;
use App\Models\Competicao;
use App\DTOs\Admin\ProvaDTO;
use Core\Exceptions\HttpException;

class ProvaService
{
    /**
     * Retorna as provas de uma competição.
     */
    public function getByCompeticao(int $competicaoId)
    {
        return (new Prova())->where('competicao_id', $competicaoId)->get();
    }

    /**
     * Adiciona uma nova prova a uma competição.
     */
    public function create(int $competicaoId, ProvaDTO $dto): Prova
    {
        // Verifica se a competição existe
        (new Competicao())->findOrFail($competicaoId);

        $prova = new Prova();
        $prova->competicao_id = $competicaoId;
        $prova->categoria_id = $dto->categoria_id;
        $prova->aparelho = $dto->aparelho;
        $prova->tipo_calculo = $dto->tipo_calculo;
        $prova->num_jurados = $dto->num_jurados;
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
}
