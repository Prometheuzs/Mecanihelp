# Mecanihelp - Sistema de Chamados de Oficina Mecânica

Este é o MVP (Minimum Viable Product) do sistema **Mecanihelp**, desenvolvido como projeto prático para a unidade curricular de "Projeto de Back-End" (SENAI-SC). O sistema gerencia chamados de clientes (solicitantes) para oficinas mecânicas.

## 🚀 Tecnologias e Stack
- **Linguagem:** PHP 8.1+ (Puro, sem frameworks)
- **Banco de Dados:** MySQL / MariaDB (via PDO)
- **Front-end:** HTML5 e CSS3 Puro (Minimalista usando HSL)
- **Arquitetura:** MVC simplificado em camadas (Rotas, Services e Repositories)
- **Segurança:** CSRF Tokens, Escape HMTL, Prepared Statements.

---

## 🛠️ Como rodar o projeto

**1. Pré-requisitos:**
Ter instalado na máquina XAMPP, WAMP, Laragon, ou possuir PHP CLI e MySQL Server configurados.

**2. Clone e Banco de Dados:**
- Clone este repositório no seu diretório local.
- No seu MySQL (via phpMyAdmin ou DBeaver), rode os arquivos em ordem:
  1. `database/schema.sql` (Cria o banco de dados `mecanihelp` e as tabelas vazias)
  2. `database/seed.sql` (Popula com dados fictícios de demonstração para a banca)

**3. Configuração do Back-end:**
- Acesse a pasta `app/config/`.
- Copie o arquivo `config.example.php` e renomeie para `config.php`.
- Edite `config.php` informando a sua senha do MySQL (se necessário) e ajuste a constante `BASE_URL` para o endereço do seu ambiente (ex: `http://localhost/Mecanihelp/public`).

**4. Rodando:**
- Inicie os serviços do Apache/MySQL via XAMPP ou execute o comando na raiz do projeto:
  `php -S localhost:8000 -t public`
- Acesse: `http://localhost:8000/`

---

## 🔑 Usuários para Teste de Apresentação (Seed)
*A senha padrão para TODOS os usuários abaixo é: `password`*

| Perfil | Login |
|---|---|
| **Administrador** | `admin` |
| **Atendente** (Mecânico) | `joao` ou `maria` |
| **Solicitante** (Cliente) | `carlos` ou `ana` |

---

## 📁 Arquitetura e Estrutura de Pastas
O projeto foi construído separando fortemente Regras de Negócio, Acesso a Dados e Visualização:

- `public/`: Única pasta exposta web. Guarda o Front Controller (`index.php`), telas e `assets/css`. Os arquivos recebem o POST/GET, acionam o *Service* adequado e injetam a view.
- `app/config/`: Conexão com PDO.
- `app/core/`: Funções globais (`Auth`, `Csrf`, `Session`, etc).
- `app/services/`: Coração do projeto. Aqui ficam todas as validações e regras de negócio (`ChamadoService`).
- `app/repositories/`: As únicas classes que conversam com o Banco (`PDO` puro).
- `app/views/layouts/`: Pedaços visuais injetados (`header`, `menu`, `footer`) pelas telas públicas para evitar duplicação.

---

## 📜 Regras de Negócio Implementadas

| Regra | Arquivo Central |
|---|---|
| **RN01**: Só pode encerrar se a retirada foi confirmada | `app/services/ChamadoService.php` |
| **RN02**: Chamado deve ter veículo do próprio solicitante | `app/services/ChamadoService.php` |
| **RN03**: Apenas responsável/admin altera status | `app/services/ChamadoService.php` |
| **RN04**: Apenas 1 atendente por chamado | `app/repositories/ChamadoRepository.php` (no UPDATE) |
| **RN05**: Inserção de Peças/Serviços | *(Fora do escopo do MVP, consultar aba de limitações)* |
| **RN06**: Mudança de status exige descrição | `app/services/ChamadoService.php` |
| **RN07**: Andamento registra usuário e hora automático | `app/services/ChamadoService.php` (Passa via sessão) |
| **RN08**: Sem exclusão física de chamados | Schema s/ rotinas de CASCADE DELETE |
| **RN09**: Cliente (Solicitante) só vê os dele | `public/chamados/index.php` e Service |
| **RN10**: Apenas admin cria usuários | `public/admin/usuarios.php` |

---

## 🧱 O que ficou fora do MVP (Limitações)
- A associação de Peças, Serviços e orçamentos dentro do Chamado (`RN05`) não foi implementada nas views para focar no fluxo primordial de atendimento. O banco suporta a arquitetura, mas a tela não foi desenvolvida nesta sprint.
- Auto-cadastro externo de novos clientes (eles são cadastrados apenas pelo admin nesta versão).
- Envio de notificações push ou e-mails em tempo real.

---

## 🚨 Dificuldades Encontradas e Soluções
*(Espaço reservado para a equipe preencher após discussão técnica)*

1. **Dificuldade:** 
   - **Solução:** 
2. **Dificuldade:** 
   - **Solução:** 

---

## 📌 Quadro de Tarefas (Kanban)
*(Espaço reservado para a equipe)*

- **[Nome 1]**: Modelagem do Banco, Configuração PDO e Autenticação.
- **[Nome 2]**: Telas HSL, Frontend puro e Layouts.
- **[Nome 3]**: Repositórios e Fluxo de criação de chamados.
- **[Nome 4]**: Regras de Negócio no ChamadoService e Apresentação.
