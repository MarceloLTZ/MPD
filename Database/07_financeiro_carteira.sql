USE marido_de_alguel_master;

CREATE TABLE Pagamento(
    idPagamento INT AUTO_INCREMENT PRIMARY KEY,

    ValorTotal DECIMAL(10,2) NOT NULL,
    TaxaPlataforma DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    ValorRepassePrestador DECIMAL (10,2) NOT NULL DEFAULT 0.00,

    MetodoPagamento ENUM(
        'PIX',
        'Cartao_Credito',
        'Cartao_Credito',
        'Dinheiro'
    )NOT NULL,

    DataPagamento DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    StatusPagamento ENUM(
        'Pendente',
        'Aprovado',
        'Recusado',
        'Estornado'
    )DEFAULT 'Pendente',

    Agendamento_idAgendamento INT UNIQUE NOT NULL,

    CONSTRAINT fk_pagamento_agendamento
        FOREIGN KEY (Agendamento_idAgendamento)
        REFERENCES Agendamento(idAgendamento)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    INDEX idx_pagamento_status (StatusPagamento),
    INDEX idx_pagamento_data (DataPagamento)
) ENGINE= InnoDB;


CREATE TABLE CarteiraPrestador (
    idCarteira INT AUTO_INCREMENT PRIMARY KEY,

    Profisional_idProfissional INT UNIQUE NOT NULL,

    SaldoDisponivel DECIMAL(10,2) DEFAULT 0.00,
    SaldoBloqueado DECIMAL(10,2) DEFAULT 0.00,

    ChavePix VARCHAR(100),

    AtualizadoEm DATETIME DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_carteira_profissional
        FOREIGN KEY (Profissional_idProfissional)
        REFERENCES Profissional(idProfissional)
        ON DELETE CASCADE

) ENGINE=InnoDB;


CREATE TABLE TransacaoCarteira (
    idTransacao INT AUTO_INCREMENT PRIMARY KEY,

    Carteira_idCarteira INT NOT NUll,

    TipoTransacao ENUM(
        'Credito_Servico',
        'Saque_Pix',
        'Estorno_Cliente',
        'Taxa_Ajuste'
    ) NOT NULL,

    Valor DECIMAL(10,2) NOT NULL,

    Descricao VARCHAR(255),

    DataTransacao DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_transacao_carteira
        FOREIGN KEY (Carteira_idCarteira)
        REFERENCES CarteiraPrestador(idCarteira)
        ON DELETE CASCADE,

    INDEX idx_transacao_carteira (
        Carteira_idCarteira
    ),

    INDEX idx_transacao_data (DataTransacao)
) ENGINE=InnoDB;


CREATE TABLE Desconto (
    idDesconto INT AUTO_INCREMENT PRIMARY KEY,

    CodigoCupom VARCHAR(50),
    ValorDesconto DECIMAL(10,2) NOT NULL,
    Quantidade INT DEFAULT 1,

    Pagamento_idPagamento INT NOT NULL,

    CONSTRAINT fk_desconto_pagamento
        FOREIGN KEY (Pagamento_idPagamento)
        REFERENCES Pagamento(idPagamento)
        ON DELETE CASCADE
        ON UPDATE CASCADE
)ENGINE=InnoDB;
