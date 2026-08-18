USE marido_de_aluguel_master;

-- ============================================================
-- ORÇAMENTOS, AGENDAMENTOS E EVIDÊNCIAS
-- ============================================================

CREATE TABLE SolicitacaoOrcamento (
    idSolicitacao INT AUTO_INCREMENT PRIMARY KEY,

    Clientes_ID_Clientes INT NOT NULL,
    Servico_idServico INT NOT NULL,
    Endereco_idEndereco INT NOT NULL,

    Titulo VARCHAR(150) NOT NULL,
    DescricaoProblema TEXT NOT NULL,

    Status ENUM(
        'Aberto',
        'Com_Propostas',
        'Convertido_Agendamento',
        'Cancelado'
    ) DEFAULT 'Aberto',

    DataCriacao DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_solicitacao_cliente
        FOREIGN KEY (Clientes_ID_Clientes)
        REFERENCES Clientes(ID_Clientes),

    CONSTRAINT fk_solicitacao_servico
        FOREIGN KEY (Servico_idServico)
        REFERENCES Servico(idServico),

    CONSTRAINT fk_solicitacao_endereco
        FOREIGN KEY (Endereco_idEndereco)
        REFERENCES Endereco(idEndereco),

    INDEX idx_solicitacao_cliente (Clientes_ID_Clientes),
    INDEX idx_solicitacao_status (Status)
) ENGINE=InnoDB;


CREATE TABLE PropostaOrcamento (
    idProposta INT AUTO_INCREMENT PRIMARY KEY,

    Solicitacao_idSolicitacao INT NOT NULL,
    Profissional_idProfissional INT NOT NULL,

    ValorProposto DECIMAL(10,2) NOT NULL,
    DescricaoProposta TEXT,
    PrazoEstimadoHoras INT,

    Status ENUM(
        'Pendente',
        'Aceita',
        'Recusada'
    ) DEFAULT 'Pendente',

    DataProposta DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_proposta_solicitacao
        FOREIGN KEY (Solicitacao_idSolicitacao)
        REFERENCES SolicitacaoOrcamento(idSolicitacao)
        ON DELETE CASCADE,

    CONSTRAINT fk_proposta_profissional
        FOREIGN KEY (Profissional_idProfissional)
        REFERENCES Profissional(idProfissional)
        ON DELETE CASCADE,

    INDEX idx_proposta_solicitacao (
        Solicitacao_idSolicitacao
    ),

    INDEX idx_proposta_profissional (
        Profissional_idProfissional
    ),

    INDEX idx_proposta_status (Status)
) ENGINE=InnoDB;


CREATE TABLE Agendamento (
    idAgendamento INT AUTO_INCREMENT PRIMARY KEY,

    DataHora DATETIME NOT NULL,

    Status ENUM(
        'Pendente',
        'Confirmado',
        'Em_Andamento',
        'Concluido',
        'Cancelado'
    ) DEFAULT 'Pendente',

    Clientes_ID_Clientes INT NOT NULL,
    Profissional_idProfissional INT NOT NULL,
    Servico_idServico INT NOT NULL,
    Proposta_idProposta INT UNIQUE,

    Observacoes TEXT,
    CriadoEm DATETIME DEFAULT CURRENT_TIMESTAMP,
    AtualizadoEm DATETIME DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_agendamento_cliente
        FOREIGN KEY (Clientes_ID_Clientes)
        REFERENCES Clientes(ID_Clientes),

    CONSTRAINT fk_agendamento_profissional
        FOREIGN KEY (Profissional_idProfissional)
        REFERENCES Profissional(idProfissional),

    CONSTRAINT fk_agendamento_servico
        FOREIGN KEY (Servico_idServico)
        REFERENCES Servico(idServico),

    CONSTRAINT fk_agendamento_proposta
        FOREIGN KEY (Proposta_idProposta)
        REFERENCES PropostaOrcamento(idProposta),

    INDEX idx_agendamento_data (DataHora),
    INDEX idx_agendamento_status (Status),
    INDEX idx_agendamento_profissional (
        Profissional_idProfissional
    ),
    INDEX idx_agendamento_cliente (
        Clientes_ID_Clientes
    )
) ENGINE=InnoDB;


CREATE TABLE FotosServico (
    idFoto INT AUTO_INCREMENT PRIMARY KEY,

    Agendamento_idAgendamento INT NOT NULL,

    UrlFoto VARCHAR(255) NOT NULL,

    TipoFoto ENUM(
        'Problema_Antes',
        'Resultado_Depois',
        'Comprovante'
    ) NOT NULL,

    DataUpload DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_fotos_agendamento
        FOREIGN KEY (Agendamento_idAgendamento)
        REFERENCES Agendamento(idAgendamento)
        ON DELETE CASCADE,

    INDEX idx_fotos_agendamento (
        Agendamento_idAgendamento
    )
) ENGINE=InnoDB;


CREATE TABLE HistoricoServico (
    idHistoricoServico INT AUTO_INCREMENT PRIMARY KEY,

    Observacoes TEXT,

    DataExecucao DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    Agendamento_idAgendamento INT UNIQUE NOT NULL,

    CONSTRAINT fk_historico_agendamento
        FOREIGN KEY (Agendamento_idAgendamento)
        REFERENCES Agendamento(idAgendamento)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;