<?php

use Core\Database\Connection;

/**
 * Adiciona índices nas colunas FK das tabelas de relacionamento via SQL.
 * Elimina full table scans nas queries mais frequentes do sistema.
 */
class AddIndexesToRelationalTables
{
    public function up(): void
    {
        $db = Connection::getInstance();

        // Índices em notas (consultada a cada avaliação)
        $db->exec("ALTER TABLE `notas`
            ADD INDEX `idx_notas_inscricao_id` (`inscricao_id`),
            ADD INDEX `idx_notas_jurado_id` (`jurado_id`),
            ADD INDEX `idx_notas_duplicata` (`inscricao_id`, `jurado_id`, `criterio`)
        ");

        // Índices em resultados (lida em toda renderização de ranking)
        $db->exec("ALTER TABLE `resultados`
            ADD INDEX `idx_resultados_nota_final` (`nota_final`)
        ");

        // Índices em inscricoes (base de quase toda query do sistema)
        $db->exec("ALTER TABLE `inscricoes`
            ADD INDEX `idx_inscricoes_prova_id` (`prova_id`),
            ADD INDEX `idx_inscricoes_atleta_id` (`atleta_id`),
            ADD INDEX `idx_inscricoes_competicao_id` (`competicao_id`),
            ADD INDEX `idx_inscricoes_ordem` (`ordem_apresentacao`)
        ");

        // Índices em designacoes_jurados (lida em cada verificação de permissão do juiz)
        $db->exec("ALTER TABLE `designacoes_jurados`
            ADD INDEX `idx_desig_prova_id` (`prova_id`),
            ADD INDEX `idx_desig_usuario_id` (`usuario_id`)
        ");

        echo "Indices adicionados nas tabelas relacionais.\n";
    }

    public function down(): void
    {
        $db = Connection::getInstance();

        $db->exec("ALTER TABLE `notas`
            DROP INDEX `idx_notas_inscricao_id`,
            DROP INDEX `idx_notas_jurado_id`,
            DROP INDEX `idx_notas_duplicata`
        ");

        $db->exec("ALTER TABLE `resultados`
            DROP INDEX `idx_resultados_nota_final`
        ");

        $db->exec("ALTER TABLE `inscricoes`
            DROP INDEX `idx_inscricoes_prova_id`,
            DROP INDEX `idx_inscricoes_atleta_id`,
            DROP INDEX `idx_inscricoes_competicao_id`,
            DROP INDEX `idx_inscricoes_ordem`
        ");

        $db->exec("ALTER TABLE `designacoes_jurados`
            DROP INDEX `idx_desig_prova_id`,
            DROP INDEX `idx_desig_usuario_id`
        ");
    }
}
