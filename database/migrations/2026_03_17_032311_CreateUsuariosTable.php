<?php

use Core\Database\Schema\Schema;
use Core\Database\Schema\Blueprint;

/**
 * Esta migration foi gerada pelo setup:auth e substituída pela
 * 032313_AddRoleToUsuariosTable.php que cria a tabela completa
 * do GymPodium (com role_id, data_nascimento, etc).
 *
 * Mantida aqui como no-op para não quebrar o histórico de migrations.
 */
class CreateUsuariosTable
{
    public function up(): void
    {
        // No-op: A tabela 'usuarios' é criada pela migration 032313_AddRoleToUsuariosTable
    }

    public function down(): void
    {
        // No-op correspondente
    }
}
