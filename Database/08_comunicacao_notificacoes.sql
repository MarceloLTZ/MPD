USE marido_de_aluguel_master;


CREATE TABLE Avaliacao (
    idAvaliacao INT AUTO_INCREMENT PRIMARY KEY,

    Nota INT NOT NULL CHECK (Nota BETWEEN 1 AND 5),
    Comentario TEXT,

    DataAvaliacao DATETIME NOT NULL
        DEFAULT CURRENT_TIME,
    
    Agendamento_idAgendamento INT UNIQUE NOT NULL,

    CONSTRAINT fk_avaliacao_agendamento
        FOREIGN KEY (Agendamento_idAgendamento)
        REFERENCES Agendamento(idAgendamento)
        ON DELETE CASCADE
) ENGINE = InnoDB;


CREATE TABLE Chat (
    idChat INT AUTO_INCREMENT PRIMARY KEY,

    Agendamento_idAgendamento INT NOT NULL,

    RemetenteTipo ENUM(
        'Cliente',
        'Profissional'
    ) NOT NULL,

    Mensagem TEXT NOT NULL,

    DataEnvio DATETIME NOT NULL
        DEFAULT CURRENT_TIME,

    Lido BOOLEAN DEFAULT FALSE,

    CONSTRAINT fk_chat_agendamento
        FOREIGN KEY (Agendamento_idAgendamento)
        REFERENCES Agendamento(idAgendamento)
        OND DELETE CASCADE,

    INDEX idx_chat_agendamento (
        Agendamento_idAgendamento
    )
) ENGINE = InnoDB;

CREATE TABLE Chat_Suporte (
    idChatSuporte INT AUTO_INCREMENT PRIMARY KEY,

    Suporte_idSuporte INT NOT NULL,
    Cliente_ID_Cliente INT NOT NULL,

    Mensagem TEXT NOT NULL,

    DataEnvio DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_chatsuporte_suporte
        FOREIGN KEY (Suporte_idSuporte)
        REFERENCES Suporte(idSuporte)
        ON DELETE CASCADE,

    CONSTRAINT fk_chatsuporte_cliente
        FOREIGN KEY (Clientes_ID_Clientes)
        REFERENCES Clientes(ID_Clientes)
        ON DELETE CASCADE,

    INDEX idx_chatsuporte_cliente (
        Clientes_ID_Clientes

    )
)ENGINE=InnoDB;

CREATE TABLE Notificacoes (
    idNotificacao INT AUTO_INCREMENT PRIMARY KEY,

    UsuarioTipo ENUM(
        'Cliente',
        'Profissional'
    ) NOT NULL,

    UsuarioID INT NULL,

    Titulo VARCHAR(100) NOT NULL,
    Mensagem TEXT NOT NULL,

    Lida BOOLEAN DEFAULT FALSE,

    DataEnvio DATETIME DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_notificacoes_usuario (
        UsuarioID,
        UsuarioTipo
    ),

    INDEX idx_notificacoes_usuario (
        UsuarioID,
        UsuarioTipo
    ),

    INDEX idx_notificacoes_lida (Lida)

)ENGINE=InnoDB;

