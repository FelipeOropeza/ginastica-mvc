<?php

namespace App\Services\Admin;

use App\Models\Equipe;
use App\DTOs\Admin\EquipeDTO;
use Core\Exceptions\HttpException;

class EquipeService
{
    public function getAll()
    {
        return (new Equipe())->all();
    }

    public function findById(int $id)
    {
        $equipe = (new Equipe())->find($id);
        if (!$equipe) {
            throw new HttpException("Equipe não encontrada.", 404);
        }
        return $equipe;
    }

    public function create(EquipeDTO $dto)
    {
        $equipe = new Equipe();
        $equipe->nome = $dto->nome;
        $equipe->cidade = $dto->cidade;
        $equipe->estado = $dto->estado;
        $equipe->cores = $dto->cores;
        $equipe->ativo = (int) $dto->ativo;
        
        return $equipe->save();
    }

    public function update(int $id, EquipeDTO $dto)
    {
        $equipe = $this->findById($id);
        $equipe->nome = $dto->nome;
        $equipe->cidade = $dto->cidade;
        $equipe->estado = $dto->estado;
        $equipe->cores = $dto->cores;
        $equipe->ativo = (int) $dto->ativo;

        return $equipe->save();
    }

    public function delete(int $id)
    {
        $equipe = $this->findById($id);
        return $equipe->delete();
    }
}
