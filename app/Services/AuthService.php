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
        
        // Define papel padrão (ID 1 = admin)
        $data['role_id'] = 1; 
        $data['ativo'] = 1;

        $id = $this->usuarioModel->insert($data);

        return $this->usuarioModel->with('role')->where('id', '=', $id)->first();
    }
}
