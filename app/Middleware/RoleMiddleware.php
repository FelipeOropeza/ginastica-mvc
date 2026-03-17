<?php

declare(strict_types=1);

namespace App\Middleware;

use Core\Http\Request;
use Core\Http\Response;
use Core\Database\Connection;

class RoleMiddleware
{
    /**
     * Verifica se o usuário logado possui a role necessária.
     * 
     * @param Request $request
     * @param callable $next
     * @param string ...$roles Roles permitidas (ex: 'admin', 'operador')
     * @return Response
     */
    public function handle(Request $request, callable $next, string ...$roles): Response
    {
        $sessionUser = session()->get('user');

        if (!$sessionUser) {
            return $this->deny($request, 'Sessão expirada. Faça login novamente.');
        }

        // Busca a role do usuário no banco (segurança extra contra manipulação de sessão)
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare("
            SELECT r.nome 
            FROM usuarios u 
            JOIN roles r ON u.role_id = r.id 
            WHERE u.id = ? 
            LIMIT 1
        ");
        $stmt->execute([$sessionUser['id']]);
        $userRole = $stmt->fetchColumn();

        if (!$userRole || !in_array($userRole, $roles)) {
            return $this->deny($request, 'Você não tem permissão para acessar esta área.', 403);
        }

        return $next($request);
    }

    private function deny(Request $request, string $message, int $code = 401): Response
    {
        if ($request->isHtmx()) {
            return response()->hxRedirect('/login');
        }

        if ($request->isAjax()) {
            return response()->json(['error' => $message], $code);
        }

        return Response::makeRedirect('/login');
    }
}
