USE marido_de_aluguel_master;

CREATE INDEX idx_agendamento_data ON Agendamento(DataHora);
CREATE INDEX idx_agendamento_status ON Agendamento(Status);
CREATE INDEX idx_profissional_nota ON Profissional(NotaMedia DESC);
CREATE INDEX idx_chat_agendamento ON Chat(Agendamento_idAgendamento);
CREATE INDEX idx_cidade_estado ON Cidade(Estado_idEstado);
CREATE INDEX idx_notificacoes_usuario ON Notificacoes(UsuarioID, UsuarioTipo);