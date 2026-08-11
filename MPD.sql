-- ============================================================

-- CRIAÇÃO DO BANCO DE DADOS E DEFINIÇÃO DE CONFIGURAÇÕES

-- ============================================================

DROP DATABASE IF EXISTS marido_de_aluguel_master;

CREATE DATABASE marido_de_aluguel_master DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE marido_de_aluguel_master;



-- ============================================================

-- 1. LOCALIZAÇÃO E ENDEREÇOS

-- ============================================================

CREATE TABLE Estado (

    idEstado INT AUTO_INCREMENT PRIMARY KEY,

    Nome VARCHAR(50) NOT NULL,

    Sigla CHAR(2) NOT NULL,

    ddd INT

) ENGINE=InnoDB;



CREATE TABLE Cidade (

    idCidade INT AUTO_INCREMENT PRIMARY KEY,

    Nome VARCHAR(100) NOT NULL,

    Estado_idEstado INT NOT NULL,

    CONSTRAINT fk_cidade_estado FOREIGN KEY (Estado_idEstado)

        REFERENCES Estado(idEstado) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB;



CREATE TABLE Endereco (

    idEndereco INT AUTO_INCREMENT PRIMARY KEY,

    Nome VARCHAR(50) COMMENT 'Ex: Casa, Trabalho, Galpão',

    Rua VARCHAR(150) NOT NULL,

    Numero VARCHAR(20) NOT NULL,

    Complemento VARCHAR(50),

    Bairro VARCHAR(100) NOT NULL,

    Cep VARCHAR(10) NOT NULL,

    Latitude DECIMAL(10, 8),

    Longitude DECIMAL(11, 8),

    Cidade_idCidade INT NOT NULL,

    CONSTRAINT fk_endereco_cidade FOREIGN KEY (Cidade_idCidade)

        REFERENCES Cidade(idCidade) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB;



-- ============================================================

-- 2. SUPORTE TÉCNICO E SEGURANÇA

-- ============================================================

CREATE TABLE Suporte (

    idSuporte INT AUTO_INCREMENT PRIMARY KEY,

    Descricao TEXT NOT NULL,

    Tipo VARCHAR(50) NOT NULL, -- Ex: Financeiro, Técnico, Reclamação

    Nivel VARCHAR(30) NOT NULL DEFAULT 'Baixo',

    StatusAtendimento VARCHAR(50) DEFAULT 'Pendente'

) ENGINE=InnoDB;



-- ============================================================

-- 3. USUÁRIOS (CLIENTES, PROFISSIONAIS E ADMINS)

-- ============================================================

CREATE TABLE Clientes (

    ID_Clientes INT AUTO_INCREMENT PRIMARY KEY,

    Nome VARCHAR(100) NOT NULL,

    CPF VARCHAR(14) UNIQUE NOT NULL,

    Email VARCHAR(100) UNIQUE NOT NULL,

    SenhaHash VARCHAR(255) NOT NULL,

    Telefone VARCHAR(20) NOT NULL,

    FotoPerfilUrl VARCHAR(255),

    Ativo BOOLEAN DEFAULT TRUE,

    CriadoEm DATETIME DEFAULT CURRENT_TIMESTAMP,

    Endereco_idEndereco INT,

    Suporte_idSuporte INT,

    CONSTRAINT fk_clientes_endereco FOREIGN KEY (Endereco_idEndereco)

        REFERENCES Endereco(idEndereco) ON DELETE SET NULL ON UPDATE CASCADE,

    CONSTRAINT fk_clientes_suporte FOREIGN KEY (Suporte_idSuporte)

        REFERENCES Suporte(idSuporte) ON DELETE SET NULL ON UPDATE CASCADE

) ENGINE=InnoDB;



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

    StatusConta ENUM('Pendente_Aprovacao', 'Ativo', 'Suspenso', 'Inativo') DEFAULT 'Pendente_Aprovacao',

    Disponivel BOOLEAN DEFAULT TRUE,

    CriadoEm DATETIME DEFAULT CURRENT_TIMESTAMP,

    Endereco_idEndereco INT,

    CONSTRAINT fk_profissional_endereco FOREIGN KEY (Endereco_idEndereco)

        REFERENCES Endereco(idEndereco) ON DELETE SET NULL ON UPDATE CASCADE

) ENGINE=InnoDB;



CREATE TABLE DisponibilidadeDoProfissional (

    idDisponibilidadeDoProfissional INT AUTO_INCREMENT PRIMARY KEY,

    DiaSemana ENUM('Domingo', 'Segunda', 'Terca', 'Quarta', 'Quinta', 'Sexta', 'Sabado') NOT NULL,

    HorarioInicio TIME NOT NULL,

    HorarioFim TIME NOT NULL,

    Profissional_idProfissional INT NOT NULL,

    CONSTRAINT fk_disponibilidade_profissional FOREIGN KEY (Profissional_idProfissional)

        REFERENCES Profissional(idProfissional) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB;



-- Sessões/Tokens de Login para Segurança (Mobile/Web)

CREATE TABLE SessoesUsuario (

    idSessao INT AUTO_INCREMENT PRIMARY KEY,

    UsuarioTipo ENUM('Cliente', 'Profissional') NOT NULL,

    UsuarioID INT NOT NULL,

    RefreshToken VARCHAR(500) NOT NULL,

    IpOrigem VARCHAR(45),

    Expiracao DATETIME NOT NULL,

    CriadoEm DATETIME DEFAULT CURRENT_TIMESTAMP

) ENGINE=InnoDB;



-- ============================================================

-- 4. CATÁLOGO DE SERVIÇOS E EMERGÊNCIAS

-- ============================================================

CREATE TABLE emergencias (

    idemergencias INT AUTO_INCREMENT PRIMARY KEY,

    Tipo VARCHAR(50) NOT NULL, -- Ex: Vazamento Grave, Curto Circuito

    Nivel VARCHAR(30) NOT NULL  -- Ex: Imediato, 24h

) ENGINE=InnoDB;



CREATE TABLE Servico (

    idServico INT AUTO_INCREMENT PRIMARY KEY,

    NomeServico VARCHAR(100) NOT NULL,

    Descricao TEXT,

    PrecoBase DECIMAL(10,2) NOT NULL,

    emergencias_idemergencias INT,

    CONSTRAINT fk_servico_emergencias FOREIGN KEY (emergencias_idemergencias)

        REFERENCES emergencias(idemergencias) ON DELETE SET NULL ON UPDATE CASCADE

) ENGINE=InnoDB;



CREATE TABLE Profissional_Especialidades (

    Profissional_idProfissional INT NOT NULL,

    Servico_idServico INT NOT NULL,

    PRIMARY KEY (Profissional_idProfissional, Servico_idServico),

    CONSTRAINT fk_esp_profissional FOREIGN KEY (Profissional_idProfissional)

        REFERENCES Profissional(idProfissional) ON DELETE CASCADE,

    CONSTRAINT fk_esp_servico FOREIGN KEY (Servico_idServico)

        REFERENCES Servico(idServico) ON DELETE CASCADE

) ENGINE=InnoDB;



-- ============================================================

-- 5. ORÇAMENTOS, AGENDAMENTOS E EVIDÊNCIAS

-- ============================================================

CREATE TABLE SolicitacaoOrcamento (

    idSolicitacao INT AUTO_INCREMENT PRIMARY KEY,

    Clientes_ID_Clientes INT NOT NULL,

    Servico_idServico INT NOT NULL,

    Endereco_idEndereco INT NOT NULL,

    Titulo VARCHAR(150) NOT NULL,

    DescricaoProblema TEXT NOT NULL,

    Status ENUM('Aberto', 'Com_Propostas', 'Convertido_Agendamento', 'Cancelado') DEFAULT 'Aberto',

    DataCriacao DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_solicitacao_cliente FOREIGN KEY (Clientes_ID_Clientes) REFERENCES Clientes(ID_Clientes),

    CONSTRAINT fk_solicitacao_servico FOREIGN KEY (Servico_idServico) REFERENCES Servico(idServico),

    CONSTRAINT fk_solicitacao_endereco FOREIGN KEY (Endereco_idEndereco) REFERENCES Endereco(idEndereco)

) ENGINE=InnoDB;



CREATE TABLE PropostaOrcamento (

    idProposta INT AUTO_INCREMENT PRIMARY KEY,

    Solicitacao_idSolicitacao INT NOT NULL,

    Profissional_idProfissional INT NOT NULL,

    ValorProposto DECIMAL(10,2) NOT NULL,

    DescricaoProposta TEXT,

    PrazoEstimadoHoras INT,

    Status ENUM('Pendente', 'Aceita', 'Recusada') DEFAULT 'Pendente',

    DataProposta DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_proposta_solicitacao FOREIGN KEY (Solicitacao_idSolicitacao) REFERENCES SolicitacaoOrcamento(idSolicitacao) ON DELETE CASCADE,

    CONSTRAINT fk_proposta_profissional FOREIGN KEY (Profissional_idProfissional) REFERENCES Profissional(idProfissional) ON DELETE CASCADE

) ENGINE=InnoDB;



CREATE TABLE Agendamento (

    idAgendamento INT AUTO_INCREMENT PRIMARY KEY,

    DataHora DATETIME NOT NULL,

    Status ENUM('Pendente', 'Confirmado', 'Em_Andamento', 'Concluido', 'Cancelado') DEFAULT 'Pendente',

    Clientes_ID_Clientes INT NOT NULL,

    Profissional_idProfissional INT NOT NULL,

    Servico_idServico INT NOT NULL,

    Proposta_idProposta INT UNIQUE, -- Nulo se for um agendamento direto

    CONSTRAINT fk_agendamento_cliente FOREIGN KEY (Clientes_ID_Clientes) REFERENCES Clientes(ID_Clientes),

    CONSTRAINT fk_agendamento_profissional FOREIGN KEY (Profissional_idProfissional) REFERENCES Profissional(idProfissional),

    CONSTRAINT fk_agendamento_servico FOREIGN KEY (Servico_idServico) REFERENCES Servico(idServico),

    CONSTRAINT fk_agendamento_proposta FOREIGN KEY (Proposta_idProposta) REFERENCES PropostaOrcamento(idProposta)

) ENGINE=InnoDB;



-- Fotos de evidência (Antes/Depois do serviço)

CREATE TABLE FotosServico (

    idFoto INT AUTO_INCREMENT PRIMARY KEY,

    Agendamento_idAgendamento INT NOT NULL,

    UrlFoto VARCHAR(255) NOT NULL,

    TipoFoto ENUM('Problema_Antes', 'Resultado_Depois', 'Comprovante') NOT NULL,

    DataUpload DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_fotos_agendamento FOREIGN KEY (Agendamento_idAgendamento)

        REFERENCES Agendamento(idAgendamento) ON DELETE CASCADE

) ENGINE=InnoDB;



CREATE TABLE HistoricoServico (

    idHistoricoServico INT AUTO_INCREMENT PRIMARY KEY,

    Observacoes TEXT,

    DataExecucao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    Agendamento_idAgendamento INT UNIQUE NOT NULL,

    CONSTRAINT fk_historico_agendamento FOREIGN KEY (Agendamento_idAgendamento)

        REFERENCES Agendamento(idAgendamento) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB;



-- ============================================================

-- 6. FINANCEIRO, DESCONTOS E CARTEIRA DA PLATAFORMA

-- ============================================================

CREATE TABLE Pagamento (

    idPagamento INT AUTO_INCREMENT PRIMARY KEY,

    ValorTotal DECIMAL(10,2) NOT NULL,

    TaxaPlataforma DECIMAL(10,2) NOT NULL COMMENT 'Valor cobrado do prestador como comissão',

    ValorRepassePrestador DECIMAL(10,2) NOT NULL COMMENT 'Valor líquido do prestador',

    MetodoPagamento ENUM('PIX', 'Cartao_Credito', 'Cartao_Debito', 'Dinheiro') NOT NULL,

    DataPagamento DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    StatusPagamento ENUM('Pendente', 'Aprovado', 'Recusado', 'Estornado') DEFAULT 'Pendente',

    Agendamento_idAgendamento INT UNIQUE NOT NULL,

    CONSTRAINT fk_pagamento_agendamento FOREIGN KEY (Agendamento_idAgendamento)

        REFERENCES Agendamento(idAgendamento) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB;



CREATE TABLE CarteiraPrestador (

    idCarteira INT AUTO_INCREMENT PRIMARY KEY,

    Profissional_idProfissional INT UNIQUE NOT NULL,

    SaldoDisponivel DECIMAL(10,2) DEFAULT 0.00,

    SaldoBloqueado DECIMAL(10,2) DEFAULT 0.00,

    ChavePix VARCHAR(100),

    CONSTRAINT fk_carteira_profissional FOREIGN KEY (Profissional_idProfissional)

        REFERENCES Profissional(idProfissional) ON DELETE CASCADE

) ENGINE=InnoDB;



CREATE TABLE TransacaoCarteira (

    idTransacao INT AUTO_INCREMENT PRIMARY KEY,

    Carteira_idCarteira INT NOT NULL,

    TipoTransacao ENUM('Credito_Servico', 'Saque_Pix', 'Estorno_Cliente', 'Taxa_Ajuste') NOT NULL,

    Valor DECIMAL(10,2) NOT NULL,

    DataTransacao DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_transacao_carteira FOREIGN KEY (Carteira_idCarteira)

        REFERENCES CarteiraPrestador(idCarteira) ON DELETE CASCADE

) ENGINE=InnoDB;



CREATE TABLE Desconto (

    idDesconto INT AUTO_INCREMENT PRIMARY KEY,

    CodigoCupom VARCHAR(50),

    ValorDesconto DECIMAL(10,2) NOT NULL,

    Quantidade INT DEFAULT 1,

    Pagamento_idPagamento INT NOT NULL,

    CONSTRAINT fk_desconto_pagamento FOREIGN KEY (Pagamento_idPagamento)

        REFERENCES Pagamento(idPagamento) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB;



-- ============================================================

-- 7. AVALIAÇÕES, CHAT E NOTIFICAÇÕES

-- ============================================================

CREATE TABLE Avaliacao (

    idAvaliacao INT AUTO_INCREMENT PRIMARY KEY,

    Nota INT NOT NULL CHECK (Nota BETWEEN 1 AND 5),

    Comentario TEXT,

    DataAvaliacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    Agendamento_idAgendamento INT UNIQUE NOT NULL,

    CONSTRAINT fk_avaliacao_agendamento FOREIGN KEY (Agendamento_idAgendamento)

        REFERENCES Agendamento(idAgendamento) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB;



CREATE TABLE Chat (

    idChat INT AUTO_INCREMENT PRIMARY KEY,

    Agendamento_idAgendamento INT NOT NULL,

    RemetenteTipo ENUM('Cliente', 'Profissional') NOT NULL,

    Mensagem TEXT NOT NULL,

    DataEnvio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    Lido BOOLEAN DEFAULT FALSE,

    CONSTRAINT fk_chat_agendamento FOREIGN KEY (Agendamento_idAgendamento)

        REFERENCES Agendamento(idAgendamento) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB;



CREATE TABLE Chat_Suporte (

    idChatSuporte INT AUTO_INCREMENT PRIMARY KEY,

    Suporte_idSuporte INT NOT NULL,

    Clientes_ID_Clientes INT NOT NULL,

    Mensagem TEXT NOT NULL,

    DataEnvio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_chatsuporte_suporte FOREIGN KEY (Suporte_idSuporte)

        REFERENCES Suporte(idSuporte) ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT fk_chatsuporte_cliente FOREIGN KEY (Clientes_ID_Clientes)

        REFERENCES Clientes(ID_Clientes) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB;



CREATE TABLE Notificacoes (

    idNotificacao INT AUTO_INCREMENT PRIMARY KEY,

    UsuarioTipo ENUM('Cliente', 'Profissional') NOT NULL,

    UsuarioID INT NOT NULL,

    Titulo VARCHAR(100) NOT NULL,

    Mensagem TEXT NOT NULL,

    Lida BOOLEAN DEFAULT FALSE,

    DataEnvio DATETIME DEFAULT CURRENT_TIMESTAMP

) ENGINE=InnoDB;



-- ============================================================

-- 8. ÍNDICES DE ALTA PERFORMANCE

-- ============================================================

CREATE INDEX idx_agendamento_data ON Agendamento(DataHora);

CREATE INDEX idx_agendamento_status ON Agendamento(Status);

CREATE INDEX idx_profissional_nota ON Profissional(NotaMedia DESC);

CREATE INDEX idx_chat_agendamento ON Chat(Agendamento_idAgendamento);

CREATE INDEX idx_cidade_estado ON Cidade(Estado_idEstado);

CREATE INDEX idx_notificacoes_usuario ON Notificacoes(UsuarioID, UsuarioTipo); 

