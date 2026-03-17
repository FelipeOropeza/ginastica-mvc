<?php

namespace App\Services\Admin;

use App\Models\Categoria;
use App\DTOs\Admin\CategoriaDTO;
use Core\Exceptions\HttpException;

class CategoriaService
{
    public function getAll()
    {
        return (new Categoria())->all();
    }

    public function findById(int $id)
    {
        $cat = (new Categoria())->find($id);
        if (!$cat) {
            throw new HttpException("Categoria não encontrada.", 404);
        }
        return $cat;
    }

    public function create(CategoriaDTO $dto)
    {
        $cat = new Categoria();
        $cat->nome = $dto->nome;
        $cat->idade_min = $dto->idade_min;
        $cat->idade_max = $dto->idade_max;
        $cat->descricao = $dto->descricao;
        
        return $cat->save();
    }

    public function update(int $id, CategoriaDTO $dto)
    {
        $cat = $this->findById($id);
        $cat->nome = $dto->nome;
        $cat->idade_min = $dto->idade_min;
        $cat->idade_max = $dto->idade_max;
        $cat->descricao = $dto->descricao;

        return $cat->save();
    }

    public function delete(int $id)
    {
        $cat = $this->findById($id);
        return $cat->delete();
    }
}
