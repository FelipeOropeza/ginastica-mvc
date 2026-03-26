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
        $query = (new Usuario())->with(['role', 'atleta.equipe.treinadores', 'treinador.equipe'])->select('usuarios.*');

        if ($role) {
            $query->join('roles', 'roles.id = usuarios.role_id')
                  ->where('roles.nome', '=', $role);
        }

        return $query->orderBy('usuarios.nome')->get();
    }

    /**
     * Retorna todos os papéis disponíveis.
     */
    public function getRoles()
    {
        return (new Role())->all();
    }

    /**
     * Cria um novo usuário e seu perfil técnico associado.
     */
    public function create(UserDTO $dto)
    {
        $usuario = new Usuario();
        $usuario->nome = $dto->nome;
        $usuario->email = $dto->email;
        $usuario->role_id = $dto->role_id;
        $usuario->ativo = (int) $dto->ativo;
        $usuario->senha = password_hash($dto->senha ?? 'gym123456', PASSWORD_DEFAULT);

        // Transaction garante que usuario + perfil técnico são criados juntos
        $usuario->transaction(function () use ($usuario) {
            $usuario->save();
            $this->syncTechnicalData($usuario, request()->get('technical') ?? []);
        });

        return true;
    }

    /**
     * Encontra um usuário pelo ID.
     */
    public function findById(int $id)
    {
        $usuario = (new Usuario())
            ->with(['role', 'atleta.equipe', 'treinador.equipe'])
            ->select('usuarios.*')
            ->where('usuarios.id', '=', $id)
            ->first();

        if (!$usuario) {
            throw new HttpException("Usuário não encontrado.", 404);
        }

        return $usuario;
    }

    /**
     * Atualiza os dados de um usuário e sincroniza perfil técnico.
     */
    public function update(int $id, UserDTO $dto)
    {
        $usuario = $this->findById($id);
        $usuario->nome = $dto->nome;
        $usuario->email = $dto->email;
        $usuario->role_id = $dto->role_id;
        $usuario->ativo = (int) $dto->ativo;

        // Transaction garante que dados do usuário e perfil técnico são atualizados juntos
        $usuario->transaction(function () use ($usuario) {
            $usuario->save();
            $this->syncTechnicalData($usuario, request()->get('technical') ?? []);
        });

        return true;
    }

    /**
     * Sincroniza dados de Atleta ou Treinador baseados no papel do usuário.
     */
    private function syncTechnicalData(Usuario $usuario, array $technicalData)
    {
        $role = (new Role())->find($usuario->role_id);
        $roleName = $role->nome ?? 'não encontrado';

        if (empty($technicalData)) {
            return;
        }

        // Converte strings vazias para null (evita erro de tipo em colunas INT/FK)
        $clean = fn(?string $val) => ($val !== null && $val !== '') ? $val : null;

        if ($roleName === 'atleta') {
            $atleta = (new \App\Models\Atleta())->where('usuario_id', '=', $usuario->id)->first() ?? new \App\Models\Atleta();
            $atleta->usuario_id = $usuario->id;
            $atleta->nome_completo = $clean($technicalData['nome_completo'] ?? null) ?? $usuario->nome;
            $atleta->equipe_id = $clean($technicalData['equipe_id'] ?? null);
            $atleta->categoria_id = $clean($technicalData['categoria_id'] ?? null);
            $atleta->cpf = $clean($technicalData['cpf'] ?? null);
            $atleta->data_nascimento = $clean($technicalData['data_nascimento'] ?? null);
            $atleta->numero_registro = $clean($technicalData['numero_registro'] ?? null);
            $atleta->ativo = $usuario->ativo;
            $atleta->save();
        } elseif ($roleName === 'treinador') {
            $treinador = (new \App\Models\Treinador())->where('usuario_id', '=', $usuario->id)->first() ?? new \App\Models\Treinador();
            $treinador->usuario_id = $usuario->id;
            $treinador->nome_completo = $clean($technicalData['nome_completo'] ?? null) ?? $usuario->nome;
            $treinador->equipe_id = $clean($technicalData['equipe_id'] ?? null);
            $treinador->cref = $clean($technicalData['cref'] ?? null);
            $treinador->especialidade = $clean($technicalData['especialidade'] ?? null);
            $treinador->ativo = $usuario->ativo;
            $treinador->save();
        }
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
