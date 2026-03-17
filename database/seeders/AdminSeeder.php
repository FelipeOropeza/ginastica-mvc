<?php

namespace App\Database\Seeders;

use Core\Database\Connection;

class AdminSeeder
{
    public function run(): void
    {
        $pdo = Connection::getInstance();

        // Busca role_id do admin
        $roleStmt = $pdo->query("SELECT id FROM roles WHERE nome = 'admin' LIMIT 1");
        $role = $roleStmt->fetch(\PDO::FETCH_ASSOC);

        if (!$role) {
            echo "  ❌ Role 'admin' não encontrada. Execute RolesSeeder primeiro.\n";
            return;
        }

        $senhaHash = password_hash('admin123', PASSWORD_BCRYPT);

        $stmt = $pdo->prepare(
            "INSERT IGNORE INTO usuarios (nome, email, senha, role_id, ativo, created_at, updated_at)
             VALUES (:nome, :email, :senha, :role_id, 1, NOW(), NOW())"
        );

        $stmt->execute([
            'nome'    => 'Administrador GymPodium',
            'email'   => 'admin@gympodium.com',
            'senha'   => $senhaHash,
            'role_id' => $role['id'],
        ]);

        echo "  ✅ Admin criado: admin@gympodium.com / admin123\n";
        echo "  ⚠️  Troque a senha após o primeiro login!\n";
    }
}
