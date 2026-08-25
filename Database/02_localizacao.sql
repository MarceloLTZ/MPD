USE   marido_de_aluguel_master;

 
CREATE TABLE Estado (
    idEstado INT AUTO_INCREMENT PRIMARY KEY,
    Nome VARCHAR(50) NOT NULL,
    Sigla CHAR(2) NOT NULL UNIQUE,
    ddd INT
) ENGINE=InnoDB;

CREATE TABLE Cidade (
    idCidade INT AUTO_INCREMENT PRIMARY KEY,
    Nome VARCHAR(100) NOT NULL,
    Estado_idEstado INT NOT NULL,

    CONSTRAINT  fk_cidade_estado
        FOREIGN KEY (Estado_idEstado)
        REFERENCES Estado(idEstado)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    UNIQUE KEY uk_cidade_estado (Nome, Estado_idEstado),
    INDEX idx_cidade_estado (Estado_idEstado)
)ENGINE=InnoDB;

CREATE TABLE Endereco (
    idEndereco INT AUTO_INCREMENT PRIMARY KEY,
    Nome VARCHAR(50) COMMENT 'Ex: Casa, Trabalho, Galpão',
    Rua  VARCHAR(150) NOT NULL,
    Numero VARCHAR(20) NOT NULL,
    Complemento VARCHAR(100),
    Bairro VARCHAR(100) NOT NULL,
    Cep VARCHAR(10) NOT NULL,
    Latitude DECIMAL(10, 8) ,
    Longitude DECIMAL (11, 8),
    Cidade_idCidade INT NOT NULL,

    CONSTRAINT fk_endereco_cidade
        FOREIGN KEY (Cidade_idCidade)
        REFERENCES Cidade(idCidade)
        ON DELETE CASCADE
        ON UPDATE CASCADE

    INDEX idx_endereco_cidade (Cidade_idCidade)
    INDEX idx_endereco_cep (Cep)
) ENGINE=InnoDB;
