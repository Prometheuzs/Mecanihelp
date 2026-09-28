-- ============================================================
-- MODELO FISICO - SISTEMA DE OFICINA (BRMW)
-- Baseado no modelo conceitual enviado
-- Script simples, sem hashing, sem configuracao de charset,
-- sem triggers/procedures/views - so tabelas, PK e FK.
-- ============================================================

CREATE DATABASE oficina_brmw;
USE oficina_brmw;

-- ============================================================
-- TABELAS PRINCIPAIS (ENTIDADES)
-- ============================================================

CREATE TABLE cliente (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(100) NOT NULL,
    cpf        VARCHAR(14)  NOT NULL,
    email      VARCHAR(100),
    fone       VARCHAR(20)
);

CREATE TABLE veiculo (
    id_veiculo INT AUTO_INCREMENT PRIMARY KEY,
    placa      VARCHAR(10) NOT NULL,
    modelo     VARCHAR(50),
    marca      VARCHAR(50),
    ano        INT
);

CREATE TABLE funcionario (
    id_funcionario INT AUTO_INCREMENT PRIMARY KEY,
    nome           VARCHAR(100) NOT NULL,
    login          VARCHAR(50)  NOT NULL,
    senha          VARCHAR(50)  NOT NULL,
    tipo           VARCHAR(30)
);

CREATE TABLE servico (
    id_servico     INT AUTO_INCREMENT PRIMARY KEY,
    nome           VARCHAR(100),
    descricao      VARCHAR(255),
    valor_mao_obra DECIMAL(10,2)
);

CREATE TABLE peca (
    id_peca            INT AUTO_INCREMENT PRIMARY KEY,
    nome               VARCHAR(100),
    descricao          VARCHAR(255),
    valor_unitario     DECIMAL(10,2),
    estoque_disponivel INT
);

CREATE TABLE chamado (
    id_chamado          INT AUTO_INCREMENT PRIMARY KEY,
    descricao_problema  VARCHAR(255),
    retirada_confirmada BOOLEAN DEFAULT FALSE,
    prioridade          VARCHAR(20),
    data_abertura       DATE,
    data_conclusao      DATE,
    prazo_estimado      DATE
);

CREATE TABLE orcamento (
    id_orcamento   INT AUTO_INCREMENT PRIMARY KEY,
    valor_total    DECIMAL(10,2),
    data_criacao   DATE,
    data_aprovacao DATE,
    aprovado       BOOLEAN DEFAULT FALSE
);

CREATE TABLE avaliacao (
    id_avaliacao   INT AUTO_INCREMENT PRIMARY KEY,
    nota           INT,
    comentario     VARCHAR(255),
    data_avaliacao DATE
);

CREATE TABLE notificacao (
    id_notificacao INT AUTO_INCREMENT PRIMARY KEY,
    tipo           VARCHAR(30),
    data_envio     DATE,
    mensagem       VARCHAR(255)
);

CREATE TABLE historico_status (
    id_historico    INT AUTO_INCREMENT PRIMARY KEY,
    status_anterior VARCHAR(30),
    status_novo     VARCHAR(30),
    data_hora       DATETIME
);

-- ============================================================
-- TABELAS DE RELACIONAMENTO (N:N)
-- Cada losango do modelo conceitual virou uma tabela aqui
-- ============================================================

-- cliente "possui" veiculo
CREATE TABLE cliente_veiculo (
    id_cliente INT NOT NULL,
    id_veiculo INT NOT NULL,
    PRIMARY KEY (id_cliente, id_veiculo),
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente),
    FOREIGN KEY (id_veiculo) REFERENCES veiculo(id_veiculo)
);

-- cliente "solicita" chamado
CREATE TABLE cliente_chamado (
    id_cliente INT NOT NULL,
    id_chamado INT NOT NULL,
    PRIMARY KEY (id_cliente, id_chamado),
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente),
    FOREIGN KEY (id_chamado) REFERENCES chamado(id_chamado)
);

-- cliente "recebe" notificacao
CREATE TABLE cliente_notificacao (
    id_cliente     INT NOT NULL,
    id_notificacao INT NOT NULL,
    PRIMARY KEY (id_cliente, id_notificacao),
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente),
    FOREIGN KEY (id_notificacao) REFERENCES notificacao(id_notificacao)
);

-- chamado "e objeto de" veiculo
CREATE TABLE chamado_veiculo (
    id_chamado INT NOT NULL,
    id_veiculo INT NOT NULL,
    PRIMARY KEY (id_chamado, id_veiculo),
    FOREIGN KEY (id_chamado) REFERENCES chamado(id_chamado),
    FOREIGN KEY (id_veiculo) REFERENCES veiculo(id_veiculo)
);

-- funcionario "atende" chamado
CREATE TABLE chamado_funcionario (
    id_chamado     INT NOT NULL,
    id_funcionario INT NOT NULL,
    PRIMARY KEY (id_chamado, id_funcionario),
    FOREIGN KEY (id_chamado) REFERENCES chamado(id_chamado),
    FOREIGN KEY (id_funcionario) REFERENCES funcionario(id_funcionario)
);

-- chamado "possui" historico_status
CREATE TABLE chamado_historico_status (
    id_chamado   INT NOT NULL,
    id_historico INT NOT NULL,
    PRIMARY KEY (id_chamado, id_historico),
    FOREIGN KEY (id_chamado) REFERENCES chamado(id_chamado),
    FOREIGN KEY (id_historico) REFERENCES historico_status(id_historico)
);

-- funcionario "registra" historico_status
CREATE TABLE funcionario_historico_status (
    id_funcionario INT NOT NULL,
    id_historico   INT NOT NULL,
    PRIMARY KEY (id_funcionario, id_historico),
    FOREIGN KEY (id_funcionario) REFERENCES funcionario(id_funcionario),
    FOREIGN KEY (id_historico) REFERENCES historico_status(id_historico)
);

-- chamado "possui" orcamento
CREATE TABLE chamado_orcamento (
    id_chamado   INT NOT NULL,
    id_orcamento INT NOT NULL,
    PRIMARY KEY (id_chamado, id_orcamento),
    FOREIGN KEY (id_chamado) REFERENCES chamado(id_chamado),
    FOREIGN KEY (id_orcamento) REFERENCES orcamento(id_orcamento)
);

-- chamado "recebe" avaliacao
CREATE TABLE chamado_avaliacao (
    id_chamado   INT NOT NULL,
    id_avaliacao INT NOT NULL,
    PRIMARY KEY (id_chamado, id_avaliacao),
    FOREIGN KEY (id_chamado) REFERENCES chamado(id_chamado),
    FOREIGN KEY (id_avaliacao) REFERENCES avaliacao(id_avaliacao)
);

-- chamado "gera" notificacao
CREATE TABLE chamado_notificacao (
    id_chamado     INT NOT NULL,
    id_notificacao INT NOT NULL,
    PRIMARY KEY (id_chamado, id_notificacao),
    FOREIGN KEY (id_chamado) REFERENCES chamado(id_chamado),
    FOREIGN KEY (id_notificacao) REFERENCES notificacao(id_notificacao)
);

-- chamado "utiliza" servico
CREATE TABLE chamado_servico (
    id_chamado INT NOT NULL,
    id_servico INT NOT NULL,
    PRIMARY KEY (id_chamado, id_servico),
    FOREIGN KEY (id_chamado) REFERENCES chamado(id_chamado),
    FOREIGN KEY (id_servico) REFERENCES servico(id_servico)
);

-- chamado "utiliza" peca
CREATE TABLE chamado_peca (
    id_chamado INT NOT NULL,
    id_peca    INT NOT NULL,
    PRIMARY KEY (id_chamado, id_peca),
    FOREIGN KEY (id_chamado) REFERENCES chamado(id_chamado),
    FOREIGN KEY (id_peca) REFERENCES peca(id_peca)
);
