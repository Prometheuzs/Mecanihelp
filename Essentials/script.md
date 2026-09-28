# PROMPT MESTRE: MVP DO SISTEMA DE CHAMADOS DE OFICINA (MECANIHELP)

## 1. PAPEL
Você é um desenvolvedor back-end PHP sênior, especialista em código limpo, arquitetura em camadas e segurança web. Vai construir, do zero, o MVP funcional de um sistema de chamados para oficina mecânica, que será apresentado e demonstrado ao vivo na unidade curricular "Projeto de Back-End" (SENAI-SC).

## 2. OBJETIVO
Entregar uma primeira versão funcional (MVP) que mostre, com funcionamento real e não apenas telas estáticas: banco de dados, back-end, telas, integração entre eles, regras de negócio e versionamento. O MVP não precisa ser o sistema completo, mas tudo o que for implementado deve funcionar de ponta a ponta (tela → PHP → MySQL → tela).

## 3. STACK OBRIGATÓRIA
- **Linguagem:** PHP 8.1+ puro (sem frameworks), com `declare(strict_types=1);` em todos os arquivos.
- **Banco:** MySQL/MariaDB, acesso via **PDO** com *prepared statements* (proibido concatenar SQL com input do usuário).
- **Front-end:** HTML5 + CSS3 puro (sem Bootstrap/Tailwind). JavaScript apenas se estritamente necessário e em arquivo separado.
- **Ambiente:** XAMPP/WAMP/Laragon ou `php -S localhost:8000 -t public`.
- **Versionamento:** Git/GitHub.
- **Sem dependências externas** (sem Composer obrigatório). Se usar autoload, que seja um `spl_autoload_register` simples.

## 4. USO OBRIGATÓRIO DE `include` E `require`
O código deve demonstrar o uso correto e intencional de cada instrução:
- `require_once`: para arquivos **indispensáveis** (configuração, conexão com o banco, classes, funções de autenticação). Se faltarem, o sistema deve parar.
- `require`: para arquivos essenciais carregados uma única vez por fluxo (ex.: bootstrap).
- `include` / `include_once`: para partes de **layout reutilizáveis** (header, sidebar/menu, footer, mensagens flash) e templates parciais.
- Nenhuma tela pode repetir cabeçalho, rodapé ou menu: tudo vem de includes em `app/views/layouts/`.
- Todos os caminhos usam `__DIR__` ou constantes de caminho (`BASE_PATH`), nunca caminhos relativos frágeis.

## 5. ARQUITETURA (MVC SIMPLES EM CAMADAS)
Estrutura de pastas sugerida:

```
mecanihelp/
├── public/                  # única pasta exposta ao navegador
│   ├── index.php            # front controller / redirecionamento
│   ├── login.php
│   ├── logout.php
│   ├── dashboard.php
│   ├── chamados/            # listar, novo, ver, atender, andamento, status, encerrar
│   ├── admin/               # usuarios (somente administrador)
│   └── assets/
│       ├── css/  (tokens.css, base.css, components.css)
│       └── js/
├── app/
│   ├── config/   (config.php, database.php)
│   ├── core/     (Auth.php, Session.php, Flash.php, Csrf.php, Validator.php)
│   ├── models/   (Usuario, Cliente, Veiculo, Chamado, Andamento, HistoricoStatus, Servico, Peca, Orcamento, Avaliacao, Notificacao)
│   ├── services/ (ChamadoService.php: concentra as regras de negócio)
│   ├── repositories/ (acesso a dados via PDO)
│   └── views/
│       ├── layouts/  (header.php, menu.php, footer.php, flash.php)
│       └── partials/
├── database/
│   ├── schema.sql
│   └── seed.sql             # dados de demonstração
├── README.md
└── .gitignore
```

Regras da arquitetura:
- **Views** só exibem dados (sem SQL, sem regra de negócio).
- **Repositories/Models** só falam com o banco.
- **Services** aplicam as regras de negócio (é aqui que "onde as regras são controladas" será explicado na apresentação).
- **Páginas em `public/`** fazem apenas: validar requisição → chamar service → incluir view.
- Toda página protegida começa com `require_once` do bootstrap + verificação de login e perfil.

## 6. BANCO DE DADOS
Parta do `Script-5.sql` (banco `mecanihelp`) e faça os ajustes abaixo, entregando `schema.sql` e `seed.sql` executáveis do zero.

**Tabelas base:** cliente, veiculo, funcionario, chamado, historico_status, servico, peca, chamado_servico, chamado_peca, orcamento, avaliacao, notificacao.

**Ajustes obrigatórios para atender o PDF do MVP:**
1. **Login do solicitante:** o solicitante precisa entrar no sistema. Escolha UMA abordagem e justifique no README:
   - (a) criar tabela única `usuario` (id, nome, login, senha_hash, perfil ENUM('solicitante','atendente','administrador'), ativo) ligada a `cliente`/`funcionario`; ou
   - (b) adicionar `login` e `senha_hash` em `cliente`.
   Preferência: (a), por simplificar autenticação e controle de acesso.
2. **Senha:** coluna `senha_hash VARCHAR(255)`, gravada com `password_hash()` e conferida com `password_verify()`. Nunca texto puro.
3. **Nova tabela `andamento`:** id_andamento, id_chamado, id_usuario, descricao (NOT NULL), data_hora (DEFAULT CURRENT_TIMESTAMP).
4. **`historico_status`:** adicionar `descricao TEXT NOT NULL` (justificativa obrigatória da mudança) e `id_usuario`.
5. **`chamado`:** adicionar `status VARCHAR(30) NOT NULL DEFAULT 'aberto'` (aberto, em_analise, em_execucao, aguardando_peca, concluido, encerrado), `motivo_encerramento TEXT NULL`, `data_encerramento DATETIME NULL`, `id_atendente` (responsável, NULL até ser assumido).
6. **Histórico geral de ações:** criar `historico_chamado` (id, id_chamado, id_usuario, acao, descricao, data_hora) para registrar TODAS as ações (abertura, assumir, andamento, mudança de status, encerramento), pois o PDF pede "a sequência de ações realizadas no chamado".
7. **Chamado nunca é excluído:** sem rotina de DELETE. Sem `ON DELETE CASCADE` em chamado. Encerrar = mudar status + motivo.
8. Índices em chaves estrangeiras e em `chamado.status`, `chamado.id_atendente`, `chamado.prioridade`.
9. `utf8mb4` / `utf8mb4_unicode_ci`.
10. **Seed de demonstração:** 1 administrador, 2 atendentes, 2 solicitantes com veículos, 2 serviços, 3 peças e 3 chamados em estados diferentes. Senhas de teste documentadas no README.

## 7. PERFIS E CONTROLE DE ACESSO
| Perfil | Pode |
|---|---|
| **Solicitante** | Login; cadastrar chamado para um veículo seu; consultar **somente os próprios** chamados; ver andamentos e histórico; (opcional) avaliar após conclusão. |
| **Atendente** | Login; listar chamados abertos/sem responsável e os atribuídos a ele; **assumir** chamado; registrar andamento; alterar status; encerrar chamado; ver histórico. |
| **Administrador** | Tudo do atendente + **criar/gerenciar usuários** (exclusivo) + ver todos os chamados + redistribuir chamado entre atendentes. |

O controle de acesso deve ser feito **no servidor** (função `Auth::requirePerfil([...])` no topo de cada página e dentro dos services), nunca apenas escondendo botões. Acesso indevido → HTTP 403 com página amigável.

## 8. FUNCIONALIDADES OBRIGATÓRIAS (CHECKLIST DA DEMONSTRAÇÃO)
Cada item abaixo será demonstrado ao vivo. Todos devem funcionar de verdade contra o banco.

1. **Acesso ao sistema:** login/logout com sessão segura; redirecionamento por perfil; demonstrar bloqueio de acesso entre perfis.
2. **Cadastro de chamado (solicitante):** formulário com veículo (select dos veículos do próprio cliente), descrição do problema, prioridade; abre com status `aberto`, data de abertura automática e registro no histórico.
3. **Consulta de chamados por perfil:** solicitante vê só os seus; atendente vê os disponíveis + os seus; admin vê todos. Com filtro por status e busca, ordenados por prioridade e prazo.
4. **Atendimento:** o atendente localiza um chamado e clica em "Assumir"; o sistema grava `id_atendente`, muda o status para `em_analise` e registra no histórico. **Um chamado só pode ter um atendente por vez.**
5. **Andamento:** o atendente registra andamento com descrição obrigatória; a tela mostra **usuário e data/hora** gravados automaticamente.
6. **Status:** alteração de status via formulário que **exige descrição**; grava em `historico_status` e `historico_chamado`. Validar transições permitidas (ex.: não voltar de `encerrado`).
7. **Encerramento:** formulário com **motivo obrigatório**; muda status para `encerrado`, grava motivo e data; **o registro permanece no banco** (sem exclusão).
8. **Histórico:** tela em linha do tempo, ordem cronológica, mostrando ação, descrição, usuário e data/hora.
9. **Administração:** CRUD de usuários acessível **apenas** ao administrador; demonstrar que atendente/solicitante recebem 403 ao tentar acessar.
10. **Integração:** todas as telas leem/gravam no MySQL. Nada de dados fixos no HTML.

## 9. REGRAS DE NEGÓCIO (implementar no `ChamadoService`)
- **RN01:** Um chamado só pode ser encerrado após confirmação da retirada do veículo pelo cliente (`retirada_confirmada = TRUE`). Se não confirmada, bloquear com mensagem clara.
- **RN02:** Todo chamado deve estar vinculado a um veículo cadastrado (placa, modelo e cliente responsável). O veículo deve pertencer ao solicitante.
- **RN03:** Somente o atendente responsável (ou o administrador) pode alterar o status de um chamado.
- **RN04:** Um chamado não pode ser atribuído a mais de um atendente ao mesmo tempo (a atribuição usa `UPDATE ... WHERE id_atendente IS NULL` dentro de transação para evitar disputa).
- **RN05:** Peças e serviços utilizados devem ser registrados no chamado antes do fechamento, para compor o valor final (se implementado no MVP; caso contrário, documentar como "fora do escopo do MVP").
- **RN06:** Toda mudança de status exige descrição.
- **RN07:** Todo andamento registra automaticamente usuário e data/hora (nunca informados pelo usuário).
- **RN08:** Chamados nunca são excluídos; encerramento exige motivo.
- **RN09:** Solicitante só enxerga os próprios chamados.
- **RN10:** Somente o administrador cria usuários.

Cada regra deve ter um comentário `// RNxx` no ponto do código onde é aplicada, para facilitar a explicação na apresentação.

## 10. DESIGN (MINIMALISTA, AZUL COM SUBTONS, TUDO EM HSL)
**Princípios:** interface limpa, muito espaço em branco, hierarquia tipográfica clara, sem sombras pesadas, sem gradientes chamativos, sem ícones desnecessários. Foco em legibilidade e usabilidade.

**Todas as cores devem ser declaradas em HSL, como variáveis CSS, em `public/assets/css/tokens.css`. Proibido usar HEX ou RGB em qualquer lugar.**

```css
:root {
  /* Azul (matiz base 215°) */
  --blue-50:  hsl(215, 100%, 97%);
  --blue-100: hsl(215, 95%, 92%);
  --blue-200: hsl(215, 90%, 84%);
  --blue-300: hsl(215, 85%, 72%);
  --blue-400: hsl(215, 80%, 60%);
  --blue-500: hsl(215, 75%, 50%);   /* cor primária */
  --blue-600: hsl(215, 80%, 42%);
  --blue-700: hsl(215, 85%, 34%);
  --blue-800: hsl(215, 88%, 26%);
  --blue-900: hsl(215, 90%, 18%);

  /* Neutros com leve tom azulado */
  --gray-50:  hsl(215, 25%, 98%);
  --gray-100: hsl(215, 20%, 95%);
  --gray-200: hsl(215, 16%, 89%);
  --gray-400: hsl(215, 12%, 62%);
  --gray-600: hsl(215, 14%, 38%);
  --gray-900: hsl(215, 25%, 14%);

  /* Semânticas (sempre em HSL, saturação contida) */
  --success: hsl(150, 55%, 38%);
  --warning: hsl(38, 90%, 48%);
  --danger:  hsl(355, 70%, 48%);

  /* Tokens de uso */
  --bg: var(--gray-50);
  --surface: hsl(0, 0%, 100%);
  --border: var(--gray-200);
  --text: var(--gray-900);
  --text-muted: var(--gray-600);
  --primary: var(--blue-500);
  --primary-hover: var(--blue-600);

  --radius: 8px;
  --space-1: .25rem; --space-2: .5rem; --space-3: 1rem; --space-4: 1.5rem; --space-5: 2.5rem;
  --font: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
}
```

**Componentes a criar (CSS reutilizável):** botão primário/secundário/perigoso, campos de formulário com foco em azul, card, tabela limpa (linhas com borda inferior, hover em `--blue-50`), badges de status (cada status com um subtom de azul ou cor semântica em HSL), alertas (flash), linha do tempo do histórico, menu lateral ou topo simples.

**Responsividade:** mobile-first, funcional a partir de 360px. Contraste mínimo WCAG AA. Labels em todos os campos, foco visível, `lang="pt-BR"`.

## 11. CLEAN CODE (OBRIGATÓRIO)
- Nomes **descritivos e em português consistente** (ou tudo em inglês; escolha um padrão e mantenha).
- Funções pequenas com **uma responsabilidade**; nada de funções com mais de ~25 linhas.
- **PSR-12** (indentação de 4 espaços, chaves, `camelCase` para métodos, `PascalCase` para classes).
- Tipagem forte: tipos de parâmetro e retorno em todas as funções/métodos.
- Sem código duplicado (DRY), sem números/strings mágicos (usar constantes/enums, ex.: `StatusChamado`).
- Sem código morto, sem `var_dump`/`echo` de debug, sem comentários óbvios. Comentar o **porquê**, não o quê.
- Tratamento de erros com `try/catch` e `PDOException` (`PDO::ERRMODE_EXCEPTION`); mensagens amigáveis ao usuário, detalhes no log.
- Early return para reduzir aninhamento.
- Cada classe/arquivo com uma responsabilidade clara.

## 12. SEGURANÇA MÍNIMA
- `password_hash` / `password_verify`; `session_regenerate_id(true)` no login.
- Prepared statements em 100% das queries.
- Escapar toda saída com `htmlspecialchars($v, ENT_QUOTES, 'UTF-8')` (helper `e()`).
- Token **CSRF** em todos os formulários POST.
- Validação no servidor (obrigatórios, tamanhos, tipos, valores permitidos de status/prioridade).
- Padrão **Post/Redirect/Get** após gravações; mensagens via flash.
- Credenciais do banco em `config.php` fora de `public/`, com `config.example.php` versionado e `config.php` no `.gitignore`.

## 13. VERSIONAMENTO (GIT)
- Sugerir estrutura de commits pequenos e semânticos (`feat:`, `fix:`, `docs:`, `refactor:`), por exemplo: `feat: cria schema do banco`, `feat: login e controle de perfil`, `feat: cadastro de chamado`, etc.
- Sugerir branches (`main`, `develop`, `feature/*`) e um `.gitignore` adequado.
- Listar a ordem recomendada de commits para a apresentação.

## 14. README.md (ENTREGÁVEL)
Deve conter: descrição do projeto; tecnologias; pré-requisitos; **passo a passo para rodar** (criar banco, importar `schema.sql` e `seed.sql`, configurar `config.php`, iniciar servidor); usuários e senhas de teste por perfil; estrutura de pastas; explicação da arquitetura; tabela de regras de negócio implementadas e onde ficam no código; diagrama/lista das principais tabelas e relacionamentos; o que ficou fora do MVP; dificuldades encontradas e soluções (espaço para a equipe preencher); divisão de tarefas/Kanban (espaço para a equipe).

## 15. O QUE O CÓDIGO DEVE FACILITAR EXPLICAR NA APRESENTAÇÃO
Gere ao final um **roteiro de apresentação** (1 página) cobrindo: organização do banco e principais relacionamentos; organização do back-end e padrão adotado; regras de negócio e onde são controladas; uso de Git; roteiro de demonstração passo a passo com os usuários de teste, na ordem do checklist da seção 8.

## 16. ORDEM DE EXECUÇÃO
Trabalhe em etapas e **confirme ao final de cada uma** antes de seguir:
1. Ajustar e entregar `schema.sql` + `seed.sql`.
2. Estrutura de pastas, `config`, conexão PDO, bootstrap, helpers (`e()`, CSRF, flash).
3. Autenticação e controle de perfil.
4. CSS (tokens, base, componentes) e layouts com `include`.
5. Chamados: cadastro → consulta → assumir → andamento → status → encerramento → histórico.
6. Administração de usuários.
7. README, roteiro de apresentação e sugestão de commits.

## 17. CRITÉRIOS DE ACEITE
- [ ] Todos os 10 itens do checklist (seção 8) funcionam ao vivo.
- [ ] Nenhuma cor fora de HSL; todas vêm de variáveis CSS.
- [ ] `require_once`/`require`/`include` usados conforme a seção 4, sem duplicação de layout.
- [ ] Nenhuma query sem prepared statement; nenhuma saída sem escape.
- [ ] Nenhum DELETE em chamados.
- [ ] Regras RN01–RN10 comentadas no código.
- [ ] Projeto roda do zero seguindo apenas o README.

## 18. FORMATO DA RESPOSTA
Entregue cada arquivo com o **caminho completo** como título e o **código completo** (sem "...", sem trechos omitidos). Ao terminar cada etapa, liste o que foi feito e o que falta. Se houver ambiguidade, escolha a opção mais simples, registre a suposição no README e continue.