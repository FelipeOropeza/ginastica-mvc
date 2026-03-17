# Task: Designação de Jurados

Este documento detalha o plano de implementação para a funcionalidade de designação de jurados para as provas de uma competição.

## 1. Análise (Analysis)
A funcionalidade permite que administradores atribuam jurados específicos para avaliar critérios determinados (Dificuldade, Execução, etc.) em uma prova (aparelho/categoria).

**Entidades Envolvidas:**
- `Prova`: O evento/aparelho configurado em uma competição.
- `Usuario`: O jurado (com role jurado).
- `DesignacaoJurado`: A tabela de ligação que define quem avalia o quê.

**Requisitos:**
- Listar jurados já designados para uma prova.
- Selecionar um jurado da lista de usuários ativos com papel 'jurado'.
- Definir o critério de avaliação (nota_d, nota_e, arbitro_superior, geral).
- Impedir duplicidade (mesmo jurado na mesma prova e critério).
- Remover designação.

## 2. Planejamento (Planning)

### Componentes a Criar/Modificar:
- **Model:** Atualizar `App\Models\JuradoDesignacao`.
- **DTO:** Criar `App\DTOs\Admin\JudgeAssignmentDTO`.
- **Service:** Criar `App\Services\Admin\JudgeAssignmentService`.
- **Controller:** Criar `App\Controllers\Admin\JudgeAssignmentController`.
- **View:** Criar `app/Views/admin/provas/designacoes.php`.

### Rotas:
- `GET /admin/provas/{id}/designacoes` -> Listagem e formulário.
- `POST /admin/provas/{id}/designacoes/store` -> Salvar designação.
- `POST /admin/designacoes/{id}/delete` -> Remover designação.

## 3. Solução (Solutioning)

### Estrutura do Service:
- `getDesignacoesByProva(int $provaId)`: Retorna as designações com dados do usuário.
- `getJuradosDisponiveis()`: Retorna usuários com role 'jurado'.
- `assign(int $provaId, JudgeAssignmentDTO $dto)`: Salva no banco.
- `unassign(int $designacaoId)`: Deleta do banco.

## 4. Implementação (Implementation)

- [x] Atualizar Model `JuradoDesignacao`.
- [x] Criar `JudgeAssignmentDTO`.
- [x] Criar `JudgeAssignmentService`.
- [x] Criar `JudgeAssignmentController`.
- [x] Criar View `admin/provas/designacoes.php`.
- [x] Testar fluxo completo. (Pronto para uso)
