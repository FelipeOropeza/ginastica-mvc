<?php

namespace App\Database\Seeders;

use Core\Database\Connection;

class CategoriasSeeder
{
    public function run(): void
    {
        $pdo = Connection::getInstance();

        // Categorias típicas da Ginástica Artística Feminina brasileira (CBG)
        $categorias = [
            ['nome' => 'Iniciação',  'idade_min' => 6,  'idade_max' => 9,  'descricao' => 'Categoria de entrada, foco em fundamentos'],
            ['nome' => 'Formação',   'idade_min' => 9,  'idade_max' => 12, 'descricao' => 'Desenvolvimento das habilidades básicas'],
            ['nome' => 'Pré-Equipe', 'idade_min' => 11, 'idade_max' => 14, 'descricao' => 'Preparação para competições regionais'],
            ['nome' => 'Juvenil I',  'idade_min' => 13, 'idade_max' => 15, 'descricao' => 'Competição juvenil primeira etapa'],
            ['nome' => 'Juvenil II', 'idade_min' => 15, 'idade_max' => 17, 'descricao' => 'Competição juvenil segunda etapa'],
            ['nome' => 'Adulto',     'idade_min' => 16, 'idade_max' => null, 'descricao' => 'Categoria adulta/sênior'],
        ];

        $stmt = $pdo->prepare(
            "INSERT IGNORE INTO categorias (nome, idade_min, idade_max, descricao, created_at, updated_at)
             VALUES (:nome, :idade_min, :idade_max, :descricao, NOW(), NOW())"
        );

        foreach ($categorias as $cat) {
            $stmt->execute($cat);
            echo "  ✅ Categoria '{$cat['nome']}' inserida.\n";
        }
    }
}
