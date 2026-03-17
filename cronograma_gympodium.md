# 🏆 GymPodium — Cronograma de Desenvolvimento

> **Projeto:** Sistema de Gerenciamento de Competições de Ginástica  
> **Stack:** PHP MVC Próprio · FrankenPHP · Mercure · HTMX · Jobs/Queue · PHPMailer · MariaDB · Redis · Docker  
> **Início estimado:** 17 de Março de 2026  
> **Data de entrega:** ~10 de Maio de 2026 (8 semanas)

---

## 📋 Visão Geral das Sprints

| Sprint | Tema | Período | Status |
|--------|------|---------|--------|
| **1** | Fundação: Banco de Dados + Autenticação | Semana 1–2 | ✅ **Concluída** |
| **2** | CRUD: Competições, Provas e Inscrições | Semana 2–3 | ⬜ Pendente |
| **3** | Interface de Avaliação dos Jurados (HTMX) | Semana 3–4 | ⬜ Pendente |
| **4** | Ranking em Tempo Real (Mercure) | Semana 4–5 | ⬜ Pendente |
| **5** | Notificações por E-mail (Jobs + Queue) | Semana 5–6 | ⬜ Pendente |
| **6** | Painel Admin + Relatórios PDF/CSV | Semana 6–7 | ⬜ Pendente |
| **7** | Polimento, Testes e Deploy | Semana 7–8 | ⬜ Pendente |

---

## 🟦 Sprint 1 — Fundação: Banco de Dados + Autenticação
**Período:** 17/03 → 24/03 (Semana 1–2)

### Objetivo
Configurar o ambiente, criar o banco de dados completo e implementar a autenticação com os 3 roles (admin, atleta, juiz).

### Tarefas

#### 🔧 Ambiente e Configuração
- [ ] Subir Docker com FrankenPHP + MariaDB + Redis (`docker-compose up -d --build`)
- [ ] Configurar [.env](file:///d:/FELIPE%20ETEC/Materia/PHP/ginastica-mvc/.env) com credenciais do banco, Redis, Mercure JWT e SMTP
- [ ] Verificar funcionamento do Worker Mode do FrankenPHP
- [ ] Rodar `php forge migrate` para criar a tabela `jobs`

#### 🗄️ Migrations (Banco de Dados)
Usar o CLI (`php forge make:migration`) para criar cada migration:

- [ ] `php forge make:migration CreateRolesTable` → tabela `roles` (admin, atleta, juiz)
- [ ] `php forge make:migration CreateUsersTable` → tabela `users` com FK para `roles`
- [ ] `php forge make:migration CreateCompetitionsTable` → nome, data, local, descrição, tipo ginástica
- [ ] `php forge make:migration CreateEventsTable` → FK `competition_id`, nome, descrição, max_participants
- [ ] `php forge make:migration CreateParticipationsTable` → FK `user_id`, `competition_id`, `event_id`
- [ ] `php forge make:migration CreateScoresTable` → FK `user_id` (juiz), `participation_id`, score, critério
- [ ] `php forge make:migration CreateResultsTable` → FK `event_id`, `user_id`, final_score, ranking
- [ ] `php forge make:migration CreateJudgeAssignmentsTable` → atribuição de juízes por evento
- [ ] `php forge migrate` para executar tudo

#### 🌱 Seeders
- [ ] `php forge make:seeder RolesSeeder` → inserir admin, atleta, juiz
- [ ] `php forge make:seeder AdminUserSeeder` → criar usuário admin padrão

#### 🔐 Autenticação (usar `php forge setup:auth`)
- [ ] Rodar `php forge setup:auth` para gerar scaffold de login/registro
- [ ] Ajustar o `AuthController` para suportar os 3 roles
- [ ] Criar Middleware `RoleMiddleware` para proteger rotas por role
  - `php forge make:middleware RoleMiddleware`
- [ ] Definir rotas em [routes/web.php](file:///d:/FELIPE%20ETEC/Materia/PHP/ginastica-mvc/routes/web.php) com grupos protegidos por role:
  ```php
  // Rotas do Admin
  Route::group('/admin', [RoleMiddleware::class . ':admin'], function() { ... });
  // Rotas do Juiz
  Route::group('/juiz', [RoleMiddleware::class . ':juiz'], function() { ... });
  // Rotas do Atleta
  Route::group('/atleta', [RoleMiddleware::class . ':atleta'], function() { ... });
  ```
- [ ] Criar Model [User](file:///d:/FELIPE%20ETEC/Materia/PHP/ginastica-mvc/core/Console/Kernel.php#113-136) com relationship `belongsTo Role`
  - `php forge make:model UserModel`
- [ ] Criar Model [Role](file:///d:/FELIPE%20ETEC/Materia/PHP/ginastica-mvc/database/seeders/RolesSeeder.php#7-30)
  - `php forge make:model RoleModel`
- [ ] Mutator para hash automático de senha: `php forge make:mutator HashSenhaMutator`
- [ ] Regra de validação customizada: `php forge make:rule SenhaForteRule`

#### 🖥️ Views de Autenticação
- [ ] View de login com design premium (dark mode, gradiente)
- [ ] View de registro de atletas
- [ ] Redirect pós-login baseado no role (admin → `/admin`, juiz → `/juiz`, atleta → `/atleta`)

---

## 🟩 Sprint 2 — CRUD: Competições, Eventos e Participações
**Período:** 24/03 → 31/03 (Semana 2–3)

### Objetivo
Permitir ao Admin criar e gerenciar competições/eventos e aos atletas se inscreverem.

### Tarefas

#### 📦 Models
- [ ] `php forge make:model CompetitionModel` → `hasMany events`, `hasMany participations`
- [ ] `php forge make:model EventModel` → `belongsTo competition`, `hasMany participations`, `hasMany judgeAssignments`
- [ ] `php forge make:model ParticipationModel` → `belongsTo user`, `belongsTo competition`, `belongsTo event`, `hasMany scores`
- [ ] `php forge make:model JudgeAssignmentModel` → `belongsTo event`, `belongsTo user` (juiz)
- [ ] `php forge make:model ScoreModel` → `belongsTo participation`, `belongsTo user` (juiz)
- [ ] `php forge make:model ResultModel` → `belongsTo event`, `belongsTo user`

#### 🎮 Controllers (Admin)
- [ ] `php forge make:controller Admin/CompetitionController`
  - `#[Get('/admin/competicoes')]` → listar todas
  - `#[Get('/admin/competicoes/criar')]` → form criar
  - `#[Post('/admin/competicoes/criar')]` → salvar
  - `#[Get('/admin/competicoes/{id}/editar')]` → form editar
  - `#[Post('/admin/competicoes/{id}/editar')]` → atualizar
  - `#[Post('/admin/competicoes/{id}/deletar')]` → remover
- [ ] `php forge make:controller Admin/EventController`
  - CRUD de eventos dentro de uma competição
  - Atribuição de juízes ao evento (`JudgeAssignment`)
- [ ] `php forge make:controller Admin/UserController`
  - Listar atletas e juízes
  - Criar juízes (admin cadastra manualmente)

#### 🎮 Controllers (Atleta)
- [ ] `php forge make:controller Atleta/InscricaoController`
  - `#[Get('/atleta/competicoes')]` → listar competições abertas (HTMX partial)
  - `#[Post('/atleta/competicoes/{id}/inscrever')]` → realizar inscrição
  - `#[Get('/atleta/minhas-inscricoes')]` → ver próprias inscrições

#### 🖥️ Views (HTMX-driven)
- [ ] Layout base admin com sidebar
- [ ] Tabela de competições com ações (editar/deletar via HTMX)
  - `hx-delete`, `hx-confirm`, `hx-swap="outerHTML"`
- [ ] Modal de criação/edição de competição (HTMX)
  - `hx-get` para abrir modal, `hx-post` para submeter
- [ ] Componente de card de competição para o atleta
  - `php forge make:component CompetitionCard`
- [ ] Componente de listagem de eventos com botão de inscrição
  - `php forge make:component EventListComponent`

#### ✅ Validações (PHP Attributes)
- [ ] Validar campos de competição (nome obrigatório, data futura, etc.)
- [ ] `php forge make:rule DataFuturaRule` → validar que a data da competição é no futuro

---

## 🟨 Sprint 3 — Sistema de Notas pelos Juízes (HTMX)
**Período:** 31/03 → 07/04 (Semana 3–4)

### Objetivo
Interface de avaliação ágil para juízes, com múltiplos critérios e HTMX para updates parciais.

### Tarefas

#### 🎮 Controllers (Juiz)
- [ ] `php forge make:controller Juiz/AvaliacaoController`
  - `#[Get('/juiz/eventos')]` → listar eventos que o juiz foi designado
  - `#[Get('/juiz/eventos/{id}/atletas')]` → listar atletas do evento (partial HTMX)
  - `#[Post('/juiz/notas')]` → submeter nota (retorna partial HTMX com confirmação)
  - Bloquear dupla avaliação (juiz só pode avaliar cada atleta uma vez por evento)

#### 📦 Models
- [ ] Adicionar método `calcularMediaNotas()` ao `ScoreModel`
- [ ] Adicionar método estático `calcularRanking(int $eventId)` ao `ResultModel`
  - Agrupa por atleta, faz média das notas de todos juízes, classifica

#### 🖥️ Views (Interface do Juiz)
- [ ] `php forge make:view juiz/dashboard` → painel com eventos atribuídos
- [ ] `php forge make:view juiz/avaliar` → interface de avaliação (tela principal)
  - Lista de atletas com campos de nota por critério (Dificuldade, Execução, Apresentação)
  - Submissão via HTMX: `hx-post`, `hx-swap="none"`, `hx-on::after-request` para feedback
  - Feedback visual instantâneo (badge "✅ Nota enviada!")
- [ ] `php forge make:component AvaliacaoFormComponent`
  - Formulário com slider ou input numérico (0-10) por critério
  - Média automática calculada em JavaScript

#### ✅ Validações de Nota
- [ ] `php forge make:rule NotaValidaRule` → validar entre 0.00 e 10.00
- [ ] `php forge make:rule JuizAutorizadoRule` → verificar se juiz está designado ao evento

#### 🔧 Service
- [ ] `php forge make:service AvaliacaoService`
  - `calcularNotaFinal(participationId)`: média ponderada das notas
  - `atualizarResultado(eventId, userId)`: atualiza/insere na tabela `results`
  - Chamado após cada nota salva pelo juiz

---

## 🟥 Sprint 4 — Ranking em Tempo Real (Mercure + HTMX)
**Período:** 07/04 → 14/04 (Semana 4–5)

### Objetivo
Usar o Mercure Hub (embutido no FrankenPHP/Caddy) para publicar atualizações de ranking ao vivo, consumidas via HTMX no front-end.

### Tarefas

#### 📡 Backend — Publicação Mercure
- [ ] No `AvaliacaoService`, após `atualizarResultado()`, usar o helper `broadcast()`:
  ```php
  broadcast(
      topic: "/ranking/evento/{$eventId}",
      data: json_encode($rankingAtualizado)
  );
  ```
- [ ] Criar endpoint que retorna o partial HTML do ranking para ser usado como fonte Mercure:
  - `#[Get('/ranking/evento/{id}/partial')]` → retorna HTML do ranking sem layout

#### 🖥️ Views — Consumo do Ranking
- [ ] `php forge make:view publico/ranking` → tela pública do ranking ao vivo
  - Usar diretiva HTMX SSE:
    ```html
    <div hx-ext="sse"
         sse-connect="/mercure?topic=/ranking/evento/{{ $eventId }}"
         sse-swap="message"
         hx-swap="innerHTML">
      <!-- ranking atualizado aqui -->
    </div>
    ```
- [ ] `php forge make:component RankingTableComponent`
  - Tabela com posição, nome do atleta, nota final, badge de medalha (🥇🥈🥉)
  - Animação CSS ao atualizar posição (transição suave)
- [ ] Tela de placar público (sem login):
  - `#[Get('/placar/{competitionId}')]` → tela de TV/projetor do ranking ao vivo

#### 📊 Histórico do Atleta
- [ ] `php forge make:controller Atleta/HistoricoController`
  - `#[Get('/atleta/historico')]` → exibir histórico de competições com notas e posições
- [ ] `php forge make:view atleta/historico` → cards com gráfico simples de evolução

---

## 🟪 Sprint 5 — Notificações por E-mail (Jobs + Queue)
**Período:** 14/04 → 21/04 (Semana 5–6)

### Objetivo
Usar o sistema de Jobs/Queue do framework para enviar e-mails de forma assíncrona (sem travar o request HTTP).

### Tarefas

#### 📧 Jobs de E-mail
- [ ] `php forge make:job NotificarInscricaoJob`
  - Dispara quando atleta se inscreve → e-mail de confirmação de inscrição
- [ ] `php forge make:job NotificarResultadoJob`
  - Dispara quando ranking é finalizado → e-mail com nota final e posição ao atleta
- [ ] `php forge make:job LembrarJuizJob`
  - Dispara 24h antes do evento → e-mail de lembrete ao juiz
- [ ] `php forge make:job NotificarAdminFinalJob`
  - Dispara quando todos os atletas de um evento foram avaliados → avisa o admin

#### 🔧 Services de E-mail
- [ ] `php forge make:service EmailService`
  - Métodos: `enviarConfirmacaoInscricao()`, `enviarResultado()`, `enviarLembreteJuiz()`
  - Usar o `MailManager` do core com PHPMailer
- [ ] Configurar templates de e-mail em HTML (views parciais):
  - `php forge make:view emails/confirmacao_inscricao`
  - `php forge make:view emails/resultado_final`
  - `php forge make:view emails/lembrete_juiz`

#### 🔗 Integração com Controllers
- [ ] No `InscricaoController`, após salvar participação:
  ```php
  dispatch(new NotificarInscricaoJob($user, $competition));
  ```
- [ ] No `AvaliacaoService`, ao finalizar ranking:
  ```php
  dispatch(new NotificarResultadoJob($atleta, $resultado));
  ```

#### ⚙️ Queue Worker
- [ ] Configurar driver de queue: `DATABASE` (mariadb) ou `REDIS` no [.env](file:///d:/FELIPE%20ETEC/Materia/PHP/ginastica-mvc/.env)
- [ ] Documentar comando para rodar o worker:
  ```bash
  php forge queue:work
  ```
- [ ] Testar falha e reprocessamento de jobs

---

## 🟫 Sprint 6 — Painel Admin + Relatórios
**Período:** 21/04 → 28/04 (Semana 6–7)

### Objetivo
Finalizar o painel administrativo completo e adicionar exportação de resultados.

### Tarefas

#### 🎛️ Dashboard Admin
- [ ] `php forge make:view admin/dashboard` — visão geral:
  - Cards: total de competições, atletas cadastrados, juízes, competições ativas
  - Lista das próximas competições com status
  - Gráfico simples JS de atletas por competição
- [ ] Listar e gerenciar todos os usuários (atletas, juízes)
  - Ativar/desativar usuário
  - Alterar role de usuário

#### 📄 Exportação de Resultados
- [ ] `php forge make:controller Admin/RelatorioController`
  - `#[Get('/admin/relatorios/competicao/{id}/csv')]` → exportar ranking em CSV
  - `#[Get('/admin/relatorios/competicao/{id}/pdf')]` → exportar em PDF (usar mPDF ou TCPDF via Composer)
- [ ] Botão de exportação nas views de resultado (HTMX para download)

#### 📊 Análise e Histórico
- [ ] View de análise de um atleta específico (admin pode ver histórico completo)
- [ ] View comparativa de atletas num evento

#### 🔐 Segurança e Auditoria
- [ ] `php forge make:migration CreateAuditLogsTable` → log de QUEM fez O QUÊ e QUANDO
- [ ] Middleware de log de ações críticas (deletar competição, alterar nota)
  - `php forge make:middleware AuditMiddleware`

---

## ⬜ Sprint 7 — Polimento, Testes e Deploy
**Período:** 28/04 → 10/05 (Semana 7–8)

### Objetivo
Revisar toda a aplicação, corrigir bugs, garantir responsividade mobile e preparar para entrega.

### Tarefas

#### 🎨 Polimento de UI/UX
- [ ] Garantir design responsivo em TODAS as views (mobile-first)
- [ ] Cards para listagens mobile (atletas veem competições em cards)
- [ ] Hamburger menu funcional no layout admin
- [ ] Micro-animações: hover em cards, transição no ranking ao vivo
- [ ] Feedback de loading HTMX (`hx-indicator`) em todos os formulários e listas

#### 🔒 Segurança Final
- [ ] Verificar CSRF em todos os formulários POST
- [ ] Garantir que middleware de role protege TODAS as rotas sensíveis
- [ ] Sanitização de inputs em todos os controllers
- [ ] Remover rotas de debug

#### 🧪 Testes Manuais (Fluxo Completo)
- [ ] Fluxo Admin: criar competição → criar evento → atribuir juiz → finalizar
- [ ] Fluxo Atleta: se inscrever → receber e-mail → ver resultado no ranking ao vivo
- [ ] Fluxo Juiz: receber lembrete → avaliar atletas → ranking atualiza em tempo real no placar
- [ ] Testar `queue:work` + jobs de e-mail disparando corretamente
- [ ] Testar Mercure: abrir 2 abas, avaliar nota, ver ranking atualizar na outra aba
- [ ] Testar exportação CSV e PDF

#### 🚀 Deploy e Documentação
- [ ] Ajustar `APP_DEBUG=false` para produção
- [ ] Verificar [storage/logs/app.log](file:///d:/FELIPE%20ETEC/Materia/PHP/ginastica-mvc/storage/logs/app.log) para erros silenciados
- [ ] Atualizar [README.md](file:///d:/FELIPE%20ETEC/Materia/PHP/ginastica-mvc/README.md) com instruções do GymPodium
- [ ] Criar arquivo `docs/COMO_USAR.md` com guia de uso por role
- [ ] `docker-compose up -d --build` final e teste em produção

---

## 🗺️ Mapa de Tecnologias por Funcionalidade

| Funcionalidade | Tecnologia do Framework |
|---|---|
| Login e roles | `setup:auth` + `RoleMiddleware` + PHP Attributes |
| CRUD de competições | Controllers + Models + QueryBuilder + HTMX parcials |
| Atribuição de juízes | `JudgeAssignment` Model + rotas admin |
| Interface de avaliação | HTMX (`hx-post`, `hx-swap`) + `AvaliacaoService` |
| Ranking ao vivo | **Mercure** (`broadcast()`) + HTMX SSE extension |
| Notificações e emails | **Jobs/Queue** + **PHPMailer** (`MailManager`) |
| Cache de ranking | **Redis Cache** (`Cache::remember()`) |
| Exportação PDF/CSV | Composer (mPDF/TCPDF) + `RelatorioController` |
| Log de auditoria | `AuditMiddleware` + tabela `audit_logs` |
| CLI scaffolding | `php forge make:*` para tudo |
| Deploy | **Docker + FrankenPHP + Worker Mode** |

---

## 📦 Comandos CLI Resumidos (Referência Rápida)

```bash
# Ambiente
docker-compose up -d --build
php forge migrate

# Scaffold por Sprint
php forge make:migration CreateRolesTable
php forge setup:auth

php forge make:model CompetitionModel
php forge make:controller Admin/CompetitionController
php forge make:view admin/competicoes/listar

php forge make:job NotificarInscricaoJob
php forge make:service AvaliacaoService
php forge make:component RankingTableComponent

# Rodar fila em background
php forge queue:work
```

---

> [!TIP]
> **Dica de ordem de execução:** Sempre crie a **migration → model → controller → view** nessa sequência. Use o `php forge make:*` para cada artefato para garantir que os stubs corretos sejam aplicados.

> [!IMPORTANT]
> **Mercure:** Certifique-se que o `MERCURE_PUBLISHER_JWT_KEY` e `MERCURE_SUBSCRIBER_JWT_KEY` estão configurados corretamente no [.env](file:///d:/FELIPE%20ETEC/Materia/PHP/ginastica-mvc/.env) antes de qualquer teste do Sprint 4.

> [!NOTE]
> **Queue Worker:** O comando `php forge queue:work` precisa ficar rodando em um processo separado (ou como serviço Docker) para que os jobs de e-mail sejam processados.
