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
        // 1. Busca todas as designações atuais da prova para validação de regras de negócio
        $designacoesAtuais = (new JuradoDesignacao())->where('prova_id', $provaId)->get();

        // 2. Se o novo critério for 'geral', não pode haver NENHUM outro juiz já designado
        if ($dto->criterio === 'geral' && !empty($designacoesAtuais)) {
            throw new HttpException("Uma prova com juiz 'Geral' não pode ter outros juízes designados. Remova os atuais primeiro.", 422);
        }

        // 3. Se já existe um juiz 'geral', não pode adicionar mais ninguém
        foreach ($designacoesAtuais as $desig) {
            if ($desig->criterio === 'geral') {
                throw new HttpException("Esta prova já possui um juiz 'Geral'. Nenhuma outra designação é permitida.", 422);
            }
            
            // Verifica se o mesmo jurado já está na mesma posição
            if ($desig->usuario_id == $dto->usuario_id && $desig->criterio == $dto->criterio) {
                throw new HttpException("Este jurado já está designado para este critério nesta prova.", 422);
            }
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
