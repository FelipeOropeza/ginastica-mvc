<?php

namespace App\Services\Admin;

use App\Models\JuradoDesignacao;
use App\Models\Usuario;
use App\Models\Role;
use App\DTOs\Admin\JudgeAssignmentDTO;
use Core\Exceptions\HttpException;

class JudgeAssignmentService
{
    /**
     * Retorna os jurados designados para uma prova.
     */
    public function getDesignacoesByProva(int $provaId)
    {
        return (new JuradoDesignacao())
            ->where('prova_id', $provaId)
            ->with('jurado')
            ->get();
    }

    /**
     * Retorna todos os usuários com papel de jurado.
     */
    public function getJuradosDisponiveis()
    {
        $roleJurado = (new Role())->where('nome', 'jurado')->first();
        
        if (!$roleJurado) {
            return [];
        }

        return (new Usuario())->where('role_id', $roleJurado->id)->where('ativo', 1)->get();
    }

    /**
     * Designa um jurado para uma prova.
     */
    public function assign(int $provaId, JudgeAssignmentDTO $dto)
    {
        // Verifica se já existe a mesma designação (mesmo jurado, mesma prova, mesmo critério)
        $exists = (new JuradoDesignacao())
            ->where('prova_id', $provaId)
            ->where('usuario_id', $dto->usuario_id)
            ->where('criterio', $dto->criterio)
            ->first();

        if ($exists) {
            throw new HttpException("Este jurado já está designado para este critério nesta prova.", 422);
        }

        $designacao = new JuradoDesignacao();
        $designacao->prova_id = $provaId;
        $designacao->usuario_id = $dto->usuario_id;
        $designacao->criterio = $dto->criterio;
        
        return $designacao->save();
    }

    /**
     * Remove uma designação.
     */
    public function unassign(int $id)
    {
        $designacao = (new JuradoDesignacao())->find($id);
        
        if ($designacao) {
            return $designacao->delete();
        }

        return false;
    }
}
