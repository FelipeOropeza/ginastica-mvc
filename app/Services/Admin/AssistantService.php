<?php

namespace App\Services\Admin;

use App\Services\GeminiService;
use Core\Database\Connection;

class AssistantService
{
    private GeminiService $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function processAdminQuery(string $query): string
    {
        // 1. Gera o SQL com o Gemini
        $sql = $this->generateSqlForQuery($query);
        
        // Tratamento da string recebida
        $sql = trim($sql);
        // Remove blocos de markdown se houver
        $sql = preg_replace('/```sql(.*?)```/s', '$1', $sql);
        $sql = preg_replace('/```(.*?)```/s', '$1', $sql);
        $sql = trim($sql);
        logger()->info("Assistente IA - SQL Gerado: " . $sql);

        if (!str_starts_with(strtoupper($sql), 'SELECT')) {
            return "Desculpe, apenas consultas de leitura (SELECT) so permitidas por segurana. Consulta gerada no foi um SELECT.";
        }

        if (preg_match('/\b(DELETE|UPDATE|DROP|INSERT|ALTER|TRUNCATE|EXEC|CREATE)\b/i', $sql)) {
             return "Desculpe, a consulta contm comandos de escrita não permitidos.";
        }

        try {
            // 2. Executar no BD local
            $pdo = Connection::getInstance();
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            
            if (empty($results)) {
                return "A consulta no retornou resultados para a busca solicitada.";
            }

            // Converter resultado para string legvel, limitando tamanho
            $limitedResults = array_slice($results, 0, 50);
            $resultsJson = json_encode($limitedResults, JSON_UNESCAPED_UNICODE);

            // 3. Devolve para o Gemini interpretar
            return $this->explainResults($query, $resultsJson);
            
        } catch (\PDOException $e) {
            logger()->error("Erro ao executar SQL gerado inteligente: " . $sql . ". Erro: " . $e->getMessage());
            return "Desculpe, encontrei um erro tcnico ao formatar os dados. Tente reescrever a pergunta.";
        } catch (\Exception $e) {
            return "Ocorreu um erro: " . $e->getMessage();
        }
    }

    private function generateSqlForQuery(string $question): string
    {
        $systemInstruction = <<<TEXT
Você é um Engenheiro de Dados e Assistente Técnico Especializado em Ginástica Artística.
Sua missão é transformar perguntas em Linguagem Natural em consultas SQL (MySQL/MariaDB) seguras e precisas.

--- DICIONÁRIO DE DADOS EXATO (USE APENAS ESTES NOMES) ---

1. TABELA `competicoes` (Possui Soft Delete: `deleted_at`)
   - Colunas: id, nome, data_inicio, data_fim, local, status ('rascunho', 'aberta', 'em_andamento', 'encerrada').

2. TABELA `atletas` (Possui Soft Delete: `deleted_at`)
   - Colunas: id, usuario_id, equipe_id, categoria_id, nome_completo, data_nascimento, cpf, numero_registro, ativo.

3. TABELA `inscricoes` (Possui Soft Delete: `deleted_at`)
   - Liga atleta a uma competição/prova.
   - Colunas: id, atleta_id, competicao_id, prova_id, ordem_apresentacao, status ('pendente', 'confirmada', 'desclassificada', 'retirada').

4. TABELA `categorias` (NÃO possui Soft Delete)
   - Colunas: id, nome, idade_min, idade_max, descricao.

5. TABELA `usuarios` (Possui Soft Delete: `deleted_at`)
   - Colunas: id, nome, email, role_id, ativo.

6. TABELA `roles` (NÃO possui Soft Delete)
   - Colunas: id, nome ('admin', 'treinador', 'atleta', 'jurado').

7. TABELA `equipes` (Possui Soft Delete: `deleted_at`)
   - Colunas: id, nome, cidade, estado, responsavel.

8. TABELA `provas` (Possui Soft Delete: `deleted_at`)
   - Aparelhos: 'solo', 'salto', 'barras_assimetricas', 'trave'.
   - Colunas: id, competicao_id, categoria_id, aparelho, encerrada.

9. TABELA `resultados` (Possui Soft Delete: `deleted_at`)
   - Colunas: id, inscricao_id, nota_d, nota_e, nota_final, classificacao, podio.

3. RELACIONAMENTOS (JOINS)
   - `atletas.usuario_id` = `usuarios.id`
   - `atletas.equipe_id` = `equipes.id`
   - `atletas.categoria_id` = `categorias.id`
   - `inscricoes.atleta_id` = `atletas.id`
   - `inscricoes.competicao_id` = `competicoes.id` (DÊ PREFERÊNCIA A ESTE PARA QUEM PARTICIPOU)
   - `inscricoes.prova_id` = `provas.id`
   - `provas.competicao_id` = `competicoes.id`
   - `resultados.inscricao_id` = `inscricoes.id`
   - `notas.inscricao_id` = `inscricoes.id`
   - `notas.jurado_id` = `usuarios.id`

--- REGRAS CRÍTICAS DE SQL ---

- IDIOMA: Use nomes de tabelas em PORTUGUÊS (ex: `competicoes`, NÃO `competitions`).
- SOFT DELETE: Sempre adicione `AND deleted_at IS NULL` para as tabelas que possuem essa coluna.
- LIMITES: Use sempre `LIMIT 20` como padrão para retornar uma lista útil de informações, a menos que a pergunta peça explicitamente por apenas 1 item (ex: "quem ganhou").
- DATA ATUAL: Para encontrar a competição mais recente, use `ORDER BY data_inicio DESC`.
- DESEMPENHO: Para ver resultados/notas, SEMPRE ligue `atletas` -> `inscricoes` -> `resultados`.
- SEGURANÇA: Responda APENAS o código SQL puro, sem explicações.
TEXT;

        return $this->gemini->generateText($question, $systemInstruction);
    }

    private function explainResults(string $question, string $data): string
    {
        $systemInstruction = "Você é um assistente de diretoria técnica focado em Ginástica. Formate os dados brutos do banco em uma resposta clara, natural e amigável em português (BR). IMPORTANTE: Responda apenas com texto puro. NÃO use negrito, NÃO use asteriscos (**), NÃO use hashtags (#) ou qualquer outra formatação Markdown. A resposta deve ser limpa.";
        $prompt = "Pergunta original: '{$question}'.\nDados do Banco (JSON): {$data}\n\nResposta formatada para o Administrador:";
        
        return $this->gemini->generateText($prompt, $systemInstruction);
    }
}
