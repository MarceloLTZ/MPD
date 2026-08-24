USE marido_de_alguel_master;


CREATE TABLE CategoriaProduto (
    idCategoria INT AUTO_INCREMENT PRIMARY KEY,

    Nome VARCHAR(100) NOT NULL UNIQUE,

    Descricao TEXT,

    Ativo BOOLEAN DEFAULT TRUE,

    CriadoEm DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


CREATE TABLE Fornecedor (
    idFornecedor INT AUTO_INCREMENT PRIMARY KEY,

    RazaoSocial VARCHAR(150) NOT NULL,
    NomeFantasia VARCHAR(150),

    CNPJ VARCHAR(18) UNIQUE,

    Email VARCHAR(150),
    Telefone VARCHAR(30),

    Contato VARCHAR(100),

    Ativo BOOLEAN DEFAULT TRUE,

    CriadoEm DATETIME DEFAULT CURRENT_TIMESTAMP,

    Endereco_idEndereco INT,

    CONSTRAINT fk_fornecedor_endereco
        FOREIGN KEY (Endereco_idEndereco)
        REFERENCES Endereco(idEndereco)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    INDEX idx_fornecedor_nome (NomeFantasia),
    INDEX idx_fornecedor_cnpj (CNPJ)
) ENGINE=InnoDB;


CREATE TABLE Produto (
    idProduto INT AUTO_INCREMENT PRIMARY KEY,

    CodigoProduto VARCHAR(50) UNIQUE NOT NULL,

    Nome VARCHAR(150) NOT NULL,

    Descricao TEXT,

    UnidadeMedida ENUM(
        'UN',
        'KG',
        'G',
        'L',
        'ML',
        'M',
        'CM',
        'CX',
        'PCT',
        'PAR'
    ) DEFAULT 'UN',

    PrecoCusto DECIMAL(10,2) DEFAULT 0.00,

    PrecoVenda DECIMAL(10,2) DEFAULT 0.00,

    EstoqueMinimo DECIMAL(10,3) DEFAULT 0.000,

    EstoqueMaximo DECIMAL(10,3) DEFAULT 0.000,

    Ativo BOOLEAN DEFAULT TRUE,

    CriadoEm DATETIME DEFAULT CURRENT_TIMESTAMP,

    AtualizadoEm DATETIME DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    Categoria_idCategoria INT,

    CONSTRAINT fk_produto_categoria
        FOREIGN KEY (Categoria_idCategoria)
        REFERENCES CategoriaProduto(idCategoria)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    INDEX idx_produto_nome (Nome),
    INDEX idx_produto_codigo (CodigoProduto),
    INDEX idx_produto_categoria (
        Categoria_idCategoria
    ),
    INDEX idx_produto_ativo (Ativo)
) ENGINE=InnoDB;


CREATE TABLE Estoque (
    idEstoque INT AUTO_INCREMENT PRIMARY KEY,

    Produto_idProduto INT UNIQUE NOT NULL,

    QuantidadeAtual DECIMAL(10,3) NOT NULL DEFAULT 0.000,

    QuantidadeReservada DECIMAL(10,3) NOT NULL DEFAULT 0.000,

    QuantidadeDisponivel DECIMAL(10,3)
        GENERATED ALWAYS AS
        (QuantidadeAtual - QuantidadeReservada)
        STORED,

    UltimaAtualizacao DATETIME
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_estoque_produto
        FOREIGN KEY (Produto_idProduto)
        REFERENCES Produto(idProduto)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT chk_estoque_quantidade
        CHECK (QuantidadeAtual >= 0),

    CONSTRAINT chk_estoque_reservada
        CHECK (QuantidadeReservada >= 0)
) ENGINE=InnoDB;


CREATE TABLE LocalEstoque (
    idLocalEstoque INT AUTO_INCREMENT PRIMARY KEY,

    Nome VARCHAR(100) NOT NULL,

    Tipo ENUM(
        'Almoxarifado',
        'Deposito',
        'Veiculo',
        'Loja',
        'Obra'
    ) DEFAULT 'Almoxarifado',

    Endereco_idEndereco INT,

    Ativo BOOLEAN DEFAULT TRUE,

    CriadoEm DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_local_estoque_endereco
        FOREIGN KEY (Endereco_idEndereco)
        REFERENCES Endereco(idEndereco)
        ON DELETE SET NULL
) ENGINE=InnoDB;


CREATE TABLE EstoqueLocal (
    idEstoqueLocal INT AUTO_INCREMENT PRIMARY KEY,

    Produto_idProduto INT NOT NULL,

    LocalEstoque_idLocalEstoque INT NOT NULL,

    Quantidade DECIMAL(10,3) NOT NULL DEFAULT 0.000,

    QuantidadeReservada DECIMAL(10,3) NOT NULL DEFAULT 0.000,

    AtualizadoEm DATETIME DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_estoquelocal_produto
        FOREIGN KEY (Produto_idProduto)
        REFERENCES Produto(idProduto)
        ON DELETE CASCADE,

    CONSTRAINT fk_estoquelocal_local
        FOREIGN KEY (LocalEstoque_idLocalEstoque)
        REFERENCES LocalEstoque(idLocalEstoque)
        ON DELETE CASCADE,

    UNIQUE KEY uk_produto_local (
        Produto_idProduto,
        LocalEstoque_idLocalEstoque
    ),

    CHECK (Quantidade >= 0),
    CHECK (QuantidadeReservada >= 0),

    INDEX idx_estoquelocal_produto (
        Produto_idProduto
    ),

    INDEX idx_estoquelocal_local (
        LocalEstoque_idLocalEstoque
    )
) ENGINE=InnoDB;


CREATE TABLE ProdutoFornecedor (
    idProdutoFornecedor INT AUTO_INCREMENT PRIMARY KEY,

    Produto_idProduto INT NOT NULL,
    Fornecedor_idFornecedor INT NOT NULL,

    CodigoFornecedor VARCHAR(100),

    PrecoCompra DECIMAL(10,2) DEFAULT 0.00,

    PrazoEntregaDias INT DEFAULT 0,

    Principal BOOLEAN DEFAULT FALSE,

    CONSTRAINT fk_prodfor_produto
        FOREIGN KEY (Produto_idProduto)
        REFERENCES Produto(idProduto)
        ON DELETE CASCADE,

    CONSTRAINT fk_prodfor_fornecedor
        FOREIGN KEY (Fornecedor_idFornecedor)
        REFERENCES Fornecedor(idFornecedor)
        ON DELETE CASCADE,

    UNIQUE KEY uk_produto_fornecedor (
        Produto_idProduto,
        Fornecedor_idFornecedor
    )
) ENGINE=InnoDB;


CREATE TABLE EntradaEstoque (
    idEntrada INT AUTO_INCREMENT PRIMARY KEY,

    NumeroDocumento VARCHAR(50),

    Fornecedor_idFornecedor INT,

    DataEntrada DATETIME DEFAULT CURRENT_TIMESTAMP,

    ValorTotal DECIMAL(10,2) DEFAULT 0.00,

    Status ENUM(
        'Pendente',
        'Concluida',
        'Cancelada'
    ) DEFAULT 'Concluida',

    Observacoes TEXT,

    UsuarioTipo ENUM(
        'Cliente',
        'Profissional',
        'Administrador'
    ),

    UsuarioID INT,

    CONSTRAINT fk_entrada_fornecedor
        FOREIGN KEY (Fornecedor_idFornecedor)
        REFERENCES Fornecedor(idFornecedor)
        ON DELETE SET NULL,

    INDEX idx_entrada_data (DataEntrada),
    INDEX idx_entrada_fornecedor (
        Fornecedor_idFornecedor
    )
) ENGINE=InnoDB;


CREATE TABLE EntradaEstoqueItem (
    idEntradaItem INT AUTO_INCREMENT PRIMARY KEY,

    Entrada_idEntrada INT NOT NULL,

    Produto_idProduto INT NOT NULL,

    LocalEstoque_idLocalEstoque INT NOT NULL,

    Quantidade DECIMAL(10,3) NOT NULL,

    PrecoUnitario DECIMAL(10,2) NOT NULL,

    Subtotal DECIMAL(10,2)
        GENERATED ALWAYS AS
        (Quantidade * PrecoUnitario)
        STORED,

    CONSTRAINT fk_entrada_item_entrada
        FOREIGN KEY (Entrada_idEntrada)
        REFERENCES EntradaEstoque(idEntrada)
        ON DELETE CASCADE,

    CONSTRAINT fk_entrada_item_produto
        FOREIGN KEY (Produto_idProduto)
        REFERENCES Produto(idProduto),

    CONSTRAINT fk_entrada_item_local
        FOREIGN KEY (LocalEstoque_idLocalEstoque)
        REFERENCES LocalEstoque(idLocalEstoque),

    CHECK (Quantidade > 0),
    CHECK (PrecoUnitario >= 0),

    INDEX idx_entrada_item_produto (
        Produto_idProduto
    )
) ENGINE=InnoDB;


CREATE TABLE RetiradaEstoque (
    idRetirada INT AUTO_INCREMENT PRIMARY KEY,

    NumeroRetirada VARCHAR(50) UNIQUE NOT NULL,

    Agendamento_idAgendamento INT,

    Profissional_idProfissional INT,

    LocalEstoque_idLocalEstoque INT NOT NULL,

    DataRetirada DATETIME DEFAULT CURRENT_TIMESTAMP,

    Motivo ENUM(
        'Uso_Servico',
        'Perda',
        'Avaria',
        'Transferencia',
        'Ajuste',
        'Outro'
    ) NOT NULL DEFAULT 'Uso_Servico',

    Status ENUM(
        'Pendente',
        'Concluida',
        'Cancelada'
    ) DEFAULT 'Pendente',

    Observacoes TEXT,

    CONSTRAINT fk_retirada_agendamento
        FOREIGN KEY (Agendamento_idAgendamento)
        REFERENCES Agendamento(idAgendamento)
        ON DELETE SET NULL,

    CONSTRAINT fk_retirada_profissional
        FOREIGN KEY (Profissional_idProfissional)
        REFERENCES Profissional(idProfissional)
        ON DELETE SET NULL,

    CONSTRAINT fk_retirada_local
        FOREIGN KEY (LocalEstoque_idLocalEstoque)
        REFERENCES LocalEstoque(idLocalEstoque),

    INDEX idx_retirada_agendamento (
        Agendamento_idAgendamento
    ),

    INDEX idx_retirada_profissional (
        Profissional_idProfissional
    ),

    INDEX idx_retirada_data (DataRetirada),

    INDEX idx_retirada_status (Status)
) ENGINE=InnoDB;


CREATE TABLE RetiradaEstoqueItem (
    idRetiradaItem INT AUTO_INCREMENT PRIMARY KEY,

    Retirada_idRetirada INT NOT NULL,

    Produto_idProduto INT NOT NULL,

    Quantidade DECIMAL(10,3) NOT NULL,

    PrecoUnitario DECIMAL(10,2) DEFAULT 0.00,

    Subtotal DECIMAL(10,2)
        GENERATED ALWAYS AS
        (Quantidade * PrecoUnitario)
        STORED,

    CONSTRAINT fk_retirada_item_retirada
        FOREIGN KEY (Retirada_idRetirada)
        REFERENCES RetiradaEstoque(idRetirada)
        ON DELETE CASCADE,

    CONSTRAINT fk_retirada_item_produto
        FOREIGN KEY (Produto_idProduto)
        REFERENCES Produto(idProduto),

    CHECK (Quantidade > 0),
    CHECK (PrecoUnitario >= 0),

    INDEX idx_retirada_item_produto (
        Produto_idProduto
    )
) ENGINE=InnoDB;


CREATE TABLE MovimentacaoEstoque (
    idMovimentacao INT AUTO_INCREMENT PRIMARY KEY,

    Produto_idProduto INT NOT NULL,

    LocalEstoque_idLocalEstoque INT NOT NULL,

    TipoMovimentacao ENUM(
        'Entrada',
        'Saida',
        'Ajuste',
        'Transferencia_Entrada',
        'Transferencia_Saida',
        'Reserva',
        'Cancelamento_Reserva'
    ) NOT NULL,

    Quantidade DECIMAL(10,3) NOT NULL,

    EstoqueAnterior DECIMAL(10,3) NOT NULL,

    EstoquePosterior DECIMAL(10,3) NOT NULL,

    Entrada_idEntrada INT,

    Retirada_idRetirada INT,

    UsuarioTipo ENUM(
        'Cliente',
        'Profissional',
        'Administrador',
        'Sistema'
    ),

    UsuarioID INT,

    DataMovimentacao DATETIME
        DEFAULT CURRENT_TIMESTAMP,

    Observacao VARCHAR(255),

    CONSTRAINT fk_mov_produto
        FOREIGN KEY (Produto_idProduto)
        REFERENCES Produto(idProduto),

    CONSTRAINT fk_mov_local
        FOREIGN KEY (LocalEstoque_idLocalEstoque)
        REFERENCES LocalEstoque(idLocalEstoque),

    CONSTRAINT fk_mov_entrada
        FOREIGN KEY (Entrada_idEntrada)
        REFERENCES EntradaEstoque(idEntrada)
        ON DELETE SET NULL,

    CONSTRAINT fk_mov_retirada
        FOREIGN KEY (Retirada_idRetirada)
        REFERENCES RetiradaEstoque(idRetirada)
        ON DELETE SET NULL,

    INDEX idx_mov_produto (
        Produto_idProduto
    ),

    INDEX idx_mov_local (
        LocalEstoque_idLocalEstoque
    ),

    INDEX idx_mov_data (
        DataMovimentacao
    ),

    INDEX idx_mov_tipo (
        TipoMovimentacao
    )
) ENGINE=InnoDB;


CREATE TABLE TransferenciaEstoque (
    idTransferencia INT AUTO_INCREMENT PRIMARY KEY,

    LocalOrigem_idLocalEstoque INT NOT NULL,

    LocalDestino_idLocalEstoque INT NOT NULL,

    DataTransferencia DATETIME DEFAULT CURRENT_TIMESTAMP,

    Status ENUM(
        'Pendente',
        'Concluida',
        'Cancelada'
    ) DEFAULT 'Pendente',

    Observacoes TEXT,

    CONSTRAINT fk_transferencia_origem
        FOREIGN KEY (LocalOrigem_idLocalEstoque)
        REFERENCES LocalEstoque(idLocalEstoque),

    CONSTRAINT fk_transferencia_destino
        FOREIGN KEY (LocalDestino_idLocalEstoque)
        REFERENCES LocalEstoque(idLocalEstoque)
) ENGINE=InnoDB;