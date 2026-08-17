CREATE TABLE Clientes (
    ID_Clientes INT AUTO_INCREMENT PRIMARY KEY,
    Nome VARCHAR(100) NOT NULL,
    CPF VARCHAR(11) UNIQUE NOT NULL,
    Email VARCHAR(100) UNIQUE NOT NULL,
    SenhaHash VARCHAR(255) NOT NULL,
    Telefone  VARCHAR(20) NOT NULL,
    FotoPerfilUrl VARCHAR(255),
    Ativo BOOLEAN DEFAULT TRUE,
    CriadoEM DATETIME DEFAULT CURRENT_TIMESTAMP,

    Endereco_idEndereco INT,
    Suporte_idSuporte INT,

    CONSTRAINT fk_clientes_endereco
        FOREIGN KEY (Endereco_idEndereco)
        REFERENCES Endereco(idEndereco)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_cliente_suporte
        FOREIGN KEY (Suporteid_Suporte)
        REFERENCES Suporte(idSuporte)
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
    
    INDEX idx_cliente_nome (Nome),
    INDEX idx_cliente_ativo (Ativo)
)ENGINE=InnoDB;


CREATE TABLE Profissional (
    idProfissional INT AUTO_INCREMENT PRIMARY KEY,
    Nome VARCHAR(100) NOT NULL,
    CPF_CNPJ VARCHAR(20) UNIQUE NOT NULL,
    Email VARCHAR(100) UNIQUE NOT NULL,
    SenhaHash VARCHAR(255) NOT NULL,
    Telefone VARCHAR(20) NOT NULL,
    FotoPerfilUrl VARCHAR(255),
    BioDescricao TEXT,
    RaioAtendimentoKm INT DEFAULT 15,
    ValorHoraBase DECIMAL(10,2) DEFAULT 0.00,
    NotaMedia DECIMAL(3,2) DEFAULT 0.00,

    StatusConta ENUM(
        'Pendente_Aprovacao',
        'Ativo',
        'Suspenso',
        'Inativo'
    ) DEFAULT 'Pendente_Aprovacao',

    Disponivel BOOLEAN DEFAULT TRUE,
    CriadoEm DATETIME DEFAULT CURRENT_TIMESTAMP,

    Endereco_idEndereco INT,

    CONSTRAINT fk_profissional_endereco
        FOREIGN KEY (Endereco_idEndereco)
        REFERENCES Endereco(idEndereco)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    INDEX idx_profissional_nome (Nome),
    INDEX idx_profissional_status (StatusConta),
    INDEX idx_profissional_disponivel (Disponivel),
    INDEX idx_profissional_nota (NotaMedia)
) ENGINE=InnoDB;


CREATE TABLE DisponibilidadeDoProfissional (
    idDisponibilidadeDoProfissional INT AUTO_INCREMENT PRIMARY KEY,

    DiaSemana ENUM(
        'Domingo',
        'Segunda',
        'Terca',
        'Quarta',
        'Quinta',
        'Sexta',
        'Sabado'
    ) NOT NULL,

    HorarioInicio TIME NOT NULL,
    HorarioFim TIME NOT NULL,

    Profissional_idProfissional INT NOT NULL,

    CONSTRAINT fk_disponibilidade_profissional
        FOREIGN KEY (Profissional_idProfissional)
        REFERENCES Profissional(idProfissional)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    INDEX idx_disponibilidade_profissional (
        Profissional_idProfissional
    )
) ENGINE=InnoDB;


CREATE TABLE SessoesUsuario (
    idSessao INT AUTO_INCREMENT PRIMARY KEY,

    UsuarioTipo ENUM(
        'Cliente',
        'Profissional'
    ) NOT NULL,

    UsuarioID INT NOT NULL,

    RefreshToken VARCHAR(500) NOT NULL,
    IpOrigem VARCHAR(45),
    UserAgent VARCHAR(500),
    Expiracao DATETIME NOT NULL,
    CriadoEm DATETIME DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_sessao_usuario (
        UsuarioID,
        UsuarioTipo
    ),

    INDEX idx_sessao_expiracao (Expiracao)
) ENGINE=InnoDB;