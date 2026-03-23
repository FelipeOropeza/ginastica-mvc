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

        $hasGeral = false;
        $hasSplit = false;

        foreach ($designacoesAtuais as $desig) {
            if ($desig->criterio === 'geral') $hasGeral = true;
            if (in_array($desig->criterio, ['nota_d', 'nota_e'])) $hasSplit = true;
            
            // Verifica se o mesmo jurado já está designado para esta prova
            if ($desig->usuario_id == $dto->usuario_id) {
                throw new HttpException("Este jurado já está designado para esta prova.", 422);
            }
        }

        // Regras de Bloqueio:
        if ($dto->criterio === 'geral' && $hasSplit) {
            throw new HttpException("Não é possível adicionar um juiz 'Geral' em uma banca que já possui juízes específicos (D/E).", 422);
        }

        if (in_array($dto->criterio, ['nota_d', 'nota_e']) && $hasGeral) {
            throw new HttpException("Não é possível adicionar juízes específicos (D/E) em uma banca que já possui um juiz 'Geral'.", 422);
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
