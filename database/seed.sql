USE mecanihelp;

-- 1. Usuários (senha padrão: password)
INSERT INTO usuario (nome, login, senha, perfil, ativo) VALUES
('Administrador', 'admin', 'password', 'administrador', 1),
('Atendente João', 'joao', 'password', 'atendente', 1),
('Atendente Maria', 'maria', 'password', 'atendente', 1),
('Solicitante Carlos', 'carlos', 'password', 'solicitante', 1),
('Solicitante Ana', 'ana', 'password', 'solicitante', 1);

-- 2. Clientes (ligados aos solicitantes)
INSERT INTO cliente (id_usuario, cpf, email, telefone) VALUES
(4, '111.111.111-11', 'carlos@email.com', '(11) 91111-1111'),
(5, '222.222.222-22', 'ana@email.com', '(11) 92222-2222');

-- 3. Veículos
INSERT INTO veiculo (placa, modelo, marca, ano, id_cliente) VALUES
('ABC-1234', 'Civic', 'Honda', 2020, 1),
('XYZ-9876', 'Corolla', 'Toyota', 2022, 2),
('DEF-5678', 'Fit', 'Honda', 2018, 1);

-- 4. Serviços
INSERT INTO servico (nome, descricao, valor_mao_obra) VALUES
('Troca de Óleo', 'Troca de óleo do motor e filtro', 150.00),
('Alinhamento', 'Alinhamento e balanceamento 3D', 200.00);

-- 5. Peças
INSERT INTO peca (nome, descricao, valor_unitario, estoque_disponivel) VALUES
('Óleo Sintético 5W40', 'Óleo para motor', 50.00, 100),
('Filtro de Óleo', 'Filtro de óleo original', 35.00, 50),
('Pastilha de Freio', 'Pastilha de freio dianteira', 120.00, 20);

-- 6. Chamados
INSERT INTO chamado (descricao_problema, prioridade, status, id_cliente, id_veiculo, id_atendente, data_abertura) VALUES
('Barulho estranho no motor e troca de óleo', 'alta', 'em_analise', 1, 1, 2, '2026-09-27 10:00:00'),
('Alinhamento puxando para a direita', 'media', 'aberto', 2, 2, NULL, '2026-09-28 08:30:00'),
('Revisão geral de 50.000km', 'baixa', 'aguardando_peca', 1, 3, 3, '2026-09-26 14:15:00');

-- 7. Histórico de Chamados
INSERT INTO historico_chamado (id_chamado, id_usuario, acao, descricao, data_hora) VALUES
(1, 4, 'Abertura', 'Chamado aberto pelo solicitante Carlos', '2026-09-27 10:00:00'),
(1, 2, 'Assumir', 'Atendente João assumiu o chamado', '2026-09-27 10:30:00'),
(1, 2, 'Alteração de Status', 'Status alterado de aberto para em_analise', '2026-09-27 10:30:00'),
(2, 5, 'Abertura', 'Chamado aberto pela solicitante Ana', '2026-09-28 08:30:00'),
(3, 4, 'Abertura', 'Chamado aberto pelo solicitante Carlos', '2026-09-26 14:15:00'),
(3, 3, 'Assumir', 'Atendente Maria assumiu o chamado', '2026-09-26 14:45:00'),
(3, 3, 'Alteração de Status', 'Status alterado para em_analise', '2026-09-26 14:45:00'),
(3, 3, 'Alteração de Status', 'Status alterado para aguardando_peca. Necessita encomendar pastilha traseira.', '2026-09-26 16:00:00');

-- 8. Andamentos
INSERT INTO andamento (id_chamado, id_usuario, descricao, data_hora) VALUES
(1, 2, 'Veículo foi inspecionado no elevador. Identificada necessidade de troca do esticador da correia.', '2026-09-27 11:00:00'),
(3, 3, 'Feito pedido da pastilha na fornecedora. Previsão de chegada em 2 dias.', '2026-09-26 16:05:00');

-- 9. Histórico Status
INSERT INTO historico_status (status_anterior, status_novo, descricao, data_hora, id_chamado, id_usuario) VALUES
('aberto', 'em_analise', 'Iniciando análise inicial do motor.', '2026-09-27 10:30:00', 1, 2),
('aberto', 'em_analise', 'Iniciando revisão.', '2026-09-26 14:45:00', 3, 3),
('em_analise', 'aguardando_peca', 'Falta pastilha traseira específica para o modelo.', '2026-09-26 16:00:00', 3, 3);
