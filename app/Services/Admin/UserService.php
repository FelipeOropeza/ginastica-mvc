<?php

namespace App\Services\Admin;

use App\Models\Usuario;
use App\Models\Role;
use App\DTOs\Admin\UserDTO;
use Core\Exceptions\HttpException;

class UserService
{
    /**
     * Retorna todos os usuários com seus respectivos papéis.
     */
    public function getAll(?string $role = null)
    {
        $query = (new Usuario())->with('role')->select('usuarios.*');

        if ($role) {
            $query->join('roles', 'roles.id = usuarios.role_id')
                  ->where('roles.nome', '=', $role);
        }

        return $query->get();
    }

    /**
     * Retorna todos os papéis disponíveis.
     */
    public function getRoles()
    {
        return (new Role())->all();
    }

    /**
     * Cria um novo usuário.
     */
    public function create(UserDTO $dto)
    {
        $usuario = new Usuario();
        $usuario->nome = $dto->nome;
        $usuario->email = $dto->email;
        $usuario->role_id = $dto->role_id;
        $usuario->ativo = (int) $dto->ativo;
        
        // Se não informar senha, gera uma padrão 'gym123456'
        $usuario->senha = password_hash($dto->senha ?? 'gym123456', PASSWORD_DEFAULT);

        return $usuario->save();
    }

    /**
     * Encontra um usuário pelo ID.
     */
    public function findById(int $id)
    {
        $usuario = (new Usuario())->with('role')->select('usuarios.*')->where('usuarios.id', $id)->first();
        if (!$usuario) {
            throw new HttpException("Usuário não encontrado.", 404);
        }

        return $usuario;
    }

    /**
     * Atualiza os dados de um usuário (nome, e-mail e papel).
     */
    public function update(int $id, UserDTO $dto)
    {
        $usuario = $this->findById($id);

        $usuario->nome = $dto->nome;
        $usuario->email = $dto->email;
        $usuario->role_id = $dto->role_id;
        $usuario->ativo = (int) $dto->ativo;

        return $usuario->save();
    }

    /**
     * Alterna o status de ativação de um usuário.
     */
    public function toggleStatus(int $id)
    {
        $usuario = $this->findById($id);
        $usuario->ativo = $usuario->ativo ? 0 : 1;
        return $usuario->save();
    }

    /**
     * Remove um usuário do sistema.
     */
    public function delete(int $id)
    {
        $usuario = $this->findById($id);
        return $usuario->delete();
    }
}
