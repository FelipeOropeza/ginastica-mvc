<?php

namespace App\Services;

use App\Models\Usuario;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;

class AuthService
{
    private Usuario $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    public function login(LoginDTO $dto): Usuario
    {
        $usuario = $this->usuarioModel->where('email', '=', $dto->email)->with('role')->first();

        if (!$usuario || !password_verify($dto->senha, $usuario->senha)) {
            fail_validation(['email' => 'As credenciais informadas são inválidas.']);
        }

        // SEMPRE regenerar a sessão após login bem-sucedido (Prevenção de Session Fixation)
        session()->regenerate();

        return $usuario;
    }

    public function registrar(RegisterDTO $dto): Usuario
    {
        $data = $dto->toArray();
        unset($data['senha_confirmacao']);
        
        // Busca o papel de atleta no banco
        $roleAtleta = (new \App\Models\Role())->where('nome', 'atleta')->first();
        
        $data['role_id'] = $roleAtleta ? $roleAtleta->id : 4; // Fallback para ID 4 se não achar
        $data['ativo'] = 1;

        $id = $this->usuarioModel->insert($data);

        // Se for atleta, cria o perfil básico na tabela 'atletas'
        if ($roleAtleta && $data['role_id'] == $roleAtleta->id) {
            (new \App\Models\Atleta())->insert([
                'usuario_id' => $id,
                'nome_completo' => $data['nome'],
                'ativo' => 1
            ]);
        }

        return $this->usuarioModel->with('role')->where('id', '=', $id)->first();
    }
}
