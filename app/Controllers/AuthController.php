<?php

namespace App\Controllers;

use Core\Http\Response;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use Core\Attributes\Route\Get;

class AuthController
{
    private \App\Services\AuthService $authService;

    public function __construct(\App\Services\AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function loginForm()
    {
        return view('auth/login');
    }

    public function login(LoginDTO $dto)
    {
        $usuario = $this->authService->login($dto);

        // Armazena na sessão
        session()->set('user', [
            'id' => $usuario->id, 
            'nome' => $usuario->nome, 
            'email' => $usuario->email,
            'role' => $usuario->role->nome ?? 'user'
        ]);

        return redirect($this->getRedirectByRole($usuario->role->nome ?? 'user'));
    }

    public function registerForm()
    {
        return view('auth/register');
    }

    public function register(RegisterDTO $dto)
    {
        $usuario = $this->authService->registrar($dto);

        session()->set('user', [
            'id' => $usuario->id, 
            'nome' => $usuario->nome, 
            'email' => $usuario->email,
            'role' => $usuario->role->nome ?? 'user'
        ]);

        return redirect($this->getRedirectByRole($usuario->role->nome ?? 'user'));
    }

    /**
     * Define o destino do redirecionamento baseado no papel do usuário.
     */
    private function getRedirectByRole(string $role): string
    {
        return match ($role) {
            'admin', 'operador' => '/admin/dashboard',
            'jurado', 'juiz'    => '/juiz/dashboard',
            'atleta'            => '/atleta/dashboard',
            default             => '/dashboard',
        };
    }

    #[Get('/logout')]
    public function logout()
    {
        session()->remove('user');
        session()->destroy();
        return Response::makeRedirect('/login');
    }
}
