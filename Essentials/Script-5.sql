use mecanihelp;

CREATE TABLE cliente (
    id_cliente INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    cpf VARCHAR(14) UNIQUE NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefone VARCHAR(20) NOT NULL
);

CREATE TABLE veiculo (
    id_veiculo INT PRIMARY KEY AUTO_INCREMENT,
    placa VARCHAR(10) UNIQUE NOT NULL,
    modelo VARCHAR(50) NOT NULL,
    marca VARCHAR(50) NOT NULL,
    ano INT NOT NULL,
    id_cliente INT NOT NULL,
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente)
);

CREATE TABLE funcionario (
    id_funcionario INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    login VARCHAR(50) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo VARCHAR(20) NOT NULL
);

CREATE TABLE chamado (
    id_chamado INT PRIMARY KEY AUTO_INCREMENT,
    descricao_problema TEXT NOT NULL,
    retirada_confirmada BOOLEAN NOT NULL DEFAULT FALSE,
    prioridade VARCHAR(20) NOT NULL,
    data_abertura DATETIME NOT NULL,
    data_conclusao DATETIME,
    prazo_estimado DATE,
    id_cliente INT NOT NULL,
    id_veiculo INT NOT NULL,
    id_funcionario INT,
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente),
    FOREIGN KEY (id_veiculo) REFERENCES veiculo(id_veiculo),
    FOREIGN KEY (id_funcionario) REFERENCES funcionario(id_funcionario)
);

CREATE TABLE historico_status (
    id_historico INT PRIMARY KEY AUTO_INCREMENT,
    status_anterior VARCHAR(30),
    status_novo VARCHAR(30) NOT NULL,
    data_hora DATETIME NOT NULL,
    id_chamado INT NOT NULL,
    id_funcionario INT NOT NULL,
    FOREIGN KEY (id_chamado) REFERENCES chamado(id_chamado),
    FOREIGN KEY (id_funcionario) REFERENCES funcionario(id_funcionario)
);

CREATE TABLE servico (
    id_servico INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    valor_mao_obra DECIMAL(10,2) NOT NULL
);

CREATE TABLE peca (
    id_peca INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    valor_unitario DECIMAL(10,2) NOT NULL,
    estoque_disponivel INT NOT NULL DEFAULT 0
);

CREATE TABLE chamado_servico (
    id_chamado INT NOT NULL,
    id_servico INT NOT NULL,
    PRIMARY KEY (id_chamado, id_servico),
    FOREIGN KEY (id_chamado) REFERENCES chamado(id_chamado),
    FOREIGN KEY (id_servico) REFERENCES servico(id_servico)
);

CREATE TABLE chamado_peca (
    id_chamado INT NOT NULL,
    id_peca INT NOT NULL,
    quantidade INT NOT NULL DEFAULT 1,
    PRIMARY KEY (id_chamado, id_peca),
    FOREIGN KEY (id_chamado) REFERENCES chamado(id_chamado),
    FOREIGN KEY (id_peca) REFERENCES peca(id_peca)
);

CREATE TABLE orcamento (
    id_orcamento INT PRIMARY KEY AUTO_INCREMENT,
    data_criacao DATETIME NOT NULL,
    data_aprovacao DATETIME,
    valor_total DECIMAL(10,2) NOT NULL,
    aprovado BOOLEAN NOT NULL DEFAULT FALSE,
    id_chamado INT NOT NULL,
    FOREIGN KEY (id_chamado) REFERENCES chamado(id_chamado)
);

CREATE TABLE avaliacao (
    id_avaliacao INT PRIMARY KEY AUTO_INCREMENT,
    nota INT NOT NULL,
    comentario TEXT,
    data_avaliacao DATETIME NOT NULL,
    id_chamado INT NOT NULL,
    FOREIGN KEY (id_chamado) REFERENCES chamado(id_chamado)
);

CREATE TABLE notificacao (
    id_notificacao INT PRIMARY KEY AUTO_INCREMENT,
    tipo VARCHAR(30) NOT NULL,
    data_envio DATETIME NOT NULL,
    mensagem TEXT NOT NULL,
    id_cliente INT NOT NULL,
    id_chamado INT,
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente),
    FOREIGN KEY (id_chamado) REFERENCES chamado(id_chamado)
);