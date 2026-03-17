<?php

namespace App\Database\Seeders;

use Core\Database\Connection;

class RolesSeeder
{
    public function run(): void
    {
        $pdo = Connection::getInstance();

        $roles = [
            ['nome' => 'admin',    'descricao' => 'Administrador do sistema — controle total'],
            ['nome' => 'operador', 'descricao' => 'Operador — gerencia competições e inscrições'],
            ['nome' => 'jurado',   'descricao' => 'Jurado — avalia atletas e registra notas'],
        ];

        $stmt = $pdo->prepare(
            "INSERT IGNORE INTO roles (nome, descricao, created_at, updated_at)
             VALUES (:nome, :descricao, NOW(), NOW())"
        );

        foreach ($roles as $role) {
            $stmt->execute($role);
            echo "  ✅ Role '{$role['nome']}' inserida.\n";
        }
    }
}
