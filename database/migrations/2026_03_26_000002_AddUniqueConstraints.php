<?php

use Core\Database\Connection;

/**
 * Adiciona constraints UNIQUE para integridade referencial via SQL.
 */
class AddUniqueConstraints
{
    public function up(): void
    {
        $db = Connection::getInstance();

        // Um atleta só pode ter um resultado por inscrição
        $db->exec("ALTER TABLE `resultados`
            ADD UNIQUE KEY `uq_resultados_inscricao` (`inscricao_id`)
        ");

        // Um atleta não pode se inscrever duas vezes na mesma prova
        $db->exec("ALTER TABLE `inscricoes`
            ADD UNIQUE KEY `uq_inscricoes_atleta_prova` (`atleta_id`, `prova_id`)
        ");

        // Um jurado não pode ser designado duas vezes para a mesma prova
        $db->exec("ALTER TABLE `designacoes_jurados`
            ADD UNIQUE KEY `uq_desig_prova_usuario` (`prova_id`, `usuario_id`)
        ");

        echo "Constraints UNIQUE adicionados.\n";
    }

    public function down(): void
    {
        $db = Connection::getInstance();

        $db->exec("ALTER TABLE `resultados` DROP INDEX `uq_resultados_inscricao`");
        $db->exec("ALTER TABLE `inscricoes` DROP INDEX `uq_inscricoes_atleta_prova`");
        $db->exec("ALTER TABLE `designacoes_jurados` DROP INDEX `uq_desig_prova_usuario`");
    }
}
