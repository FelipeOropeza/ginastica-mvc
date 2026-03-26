<?php

declare(strict_types=1);

namespace App\Services;

use Core\Database\Connection;

/**
 * Serviço simples de auditoria que escreve na tabela logs_auditoria.
 * Registra ações críticas com dados antes/depois para rastreabilidade.
 */
class AuditoriaService
{
    /**
     * Registra uma ação de auditoria.
     *
     * @param string   $acao       Identificador da ação: 'nota.reaberta', 'competicao.encerrada', etc.
     * @param string   $tabela     Tabela afetada: 'notas', 'resultados', 'competicoes'
     * @param int      $registroId ID do registro afetado
     * @param mixed    $dadosAntes Dados antes da alteração (será serializado para JSON)
     * @param mixed    $dadosDepois Dados após a alteração (será serializado para JSON)
     */
    public static function registrar(
        string $acao,
        string $tabela,
        int $registroId,
        mixed $dadosAntes = null,
        mixed $dadosDepois = null
    ): void {
        try {
            $usuarioId = session()->has('user') ? session()->get('user')['id'] : null;
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;

            $db = Connection::getInstance();
            $stmt = $db->prepare("
                INSERT INTO logs_auditoria
                    (usuario_id, acao, tabela, registro_id, dados_antes, dados_depois, ip, feito_em, created_at, updated_at)
                VALUES
                    (:usuario_id, :acao, :tabela, :registro_id, :dados_antes, :dados_depois, :ip, NOW(), NOW(), NOW())
            ");

            $stmt->execute([
                'usuario_id'   => $usuarioId,
                'acao'         => $acao,
                'tabela'       => $tabela,
                'registro_id'  => $registroId,
                'dados_antes'  => $dadosAntes !== null ? json_encode($dadosAntes) : null,
                'dados_depois' => $dadosDepois !== null ? json_encode($dadosDepois) : null,
                'ip'           => $ip,
            ]);
        } catch (\Throwable $e) {
            // Auditoria nunca deve quebrar o fluxo principal
            logger()->error('Falha ao registrar auditoria: ' . $e->getMessage());
        }
    }
}
