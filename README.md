# 🏆 GymPodium — Gestão de Competições de Ginástica

![PHP](https://img.shields.io/badge/PHP-8.5+-8892BF?style=for-the-badge&logo=php&logoColor=white)
![FrankenPHP](https://img.shields.io/badge/FrankenPHP-Worker_Mode-00ADD8?style=for-the-badge&logo=caddy&logoColor=white)
![Mercure](https://img.shields.io/badge/Mercure-Real--Time-27AE60?style=for-the-badge&logo=mercure&logoColor=white)
![HTMX](https://img.shields.io/badge/HTMX-High_Performance-3D72D7?style=for-the-badge&logo=htmx&logoColor=white)

O **GymPodium** é um sistema completo e moderno para gerenciamento de competições de ginástica, construído sobre um micro-framework MVC autoral focado em alta performance (Stateless). O projeto utiliza tecnologias de ponta como **FrankenPHP** (em Worker Mode), **HTMX** para interfaces dinâmicas e **Mercure** para atualizações de ranking em tempo real.

---

## ⚡ Diferenciais Tecnológicos

*   **Arquitetura Stateless & High Performance**: Core construído para rodar em servidores assíncronos, com injeção de dependências via Reflection API e autowiring inteligente.
*   **Ranking em Tempo Real (Mercure Hub)**: Utiliza o protocolo SSE via Mercure (embutido no Caddy do FrankenPHP) para atualizar placares e rankings instantaneamente para o público sem recarregar a página.
*   **UX Dinâmica com HTMX**: Navegação fluida e atualizações parciais de interface, eliminando a complexidade de SPAs pesadas enquanto mantém a interatividade.
*   **Worker Mode Nativo**: Integrado ao ecossistema FrankenPHP, permitindo que a aplicação permaneça na memória entre requisições, atingindo tempos de resposta na casa dos milissegundos.

---

## 🚀 Funcionalidades Principais

### 🏛️ Painel Administrativo
*   **Gestão de Competições & Provas**: CRUD completo de competições com definição de categorias, locais e datas.
*   **Atribuição de Juízes**: Designação de juízes específicos para avaliar provas ou atletas determinados.
*   **Relatórios & Exportação**: Geração de rankings e resultados finais em formatos CSV e PDF.

### ⚖️ Interface do Juiz
*   **Avaliação Ágil**: Interface otimizada via HTMX para entrada rápida de notas por critério (Dificuldade, Execução, Apresentação).
*   **Feedback Instantâneo**: Confirmação visual de envio da nota sem interrupção do fluxo de trabalho.

### 🏃 Portal do Atleta
*   **Inscrições Online**: Processo simplificado para atletas se inscreverem em competições abertas.
*   **Histórico de Performance**: Visualização detalhada de notas e evolução ao longo das competições.

---

## 🛠️ Stack Técnica

| Camada | Tecnologia |
|---|---|
| **Linguagem** | PHP 8.5+ (Strict Types) |
| **Servidor Web** | FrankenPHP / Caddy |
| **Banco de Dados** | MySQL |
| **Cache & Filas** | Redis |
| **Front-end** | HTMX + Tailwind CSS 4 + Mercure (SSE) |
| **Infraestrutura** | Docker Compose |
| **Framework** | Custom MVC (Micro-framework Stateless) |

---

## 🔧 Instalação e Configuração

### Requisitos
*   Docker & Docker Compose
*   *Ou PHP 8.5+ e Composer (para modo tradicional)*

### 1. Clonar e Instalar
```bash
git clone https://github.com/FelipeOropeza/ginastica-mvc.git gympodium
cd gympodium
composer install
```

### 2. Configuração do Ambiente
Crie o arquivo `.env` a partir do exemplo e configure suas credenciais:
```bash
cp .env.example .env
```

### 3. Subir com Docker (Recomendado)
Este comando sobe o ambiente completo com FrankenPHP (Worker Mode), Banco de Dados, Redis e o Hub Mercure:
```bash
docker-compose up -d --build
```
Acesse `http://localhost:8000`.

### 4. Migrações e Seeders
```bash
php forge migrate
php forge db:seed RolesSeeder
php forge db:seed AdminUserSeeder
```

---

## 🛠️ CLI Forge (Comandos Úteis)

O projeto conta com uma ferramenta de linha de comando poderosa para automação:

```bash
# Criação de código
php forge make:controller NomeController
php forge make:model NomeModel
php forge make:service NomeService
php forge make:migration NovaTabela

# Banco de Dados
php forge migrate          # Roda migrations pendentes
php forge migrate:refresh  # Reseta e roda as migrations novamente

# Processamento
php forge queue:work       # Inicia o worker para processar filas
php forge optimize         # Compila rotas para cache de performance
```

---

## 📄 Licença

Este projeto está sob a licença [MIT](LICENSE).

---

> Desenvolvido com foco em excelência técnica e performance por [Felipe](mailto:felipe2006.co@gmail.com).
