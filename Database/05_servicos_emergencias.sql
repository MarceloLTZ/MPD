USE marido_aluguel_master;

CREATE TABLE emergencias (
    idemergencias INT AUTO_INCREMENT PRIMARY KEY,
    Tipo VARCHAR(50) NOT NULL,
    Nivel VARCHAR(30) NOT NULL,
    Descricao TEXT,
    Ativo BOOLEAN DEFAULT TRUE
) ENGINE = InnoDBB;

CREATE TABLE Servico (
    idServico INT AUTO_INCREMENT PRIMARY KEY,
    NomeServico VARCHAR(100) NOT NULL,
    Descricao TEXT,
    PrecoBase DECIMAL(10,2) NOT NULL DEFAULT 0.00,

    emergencias_idemergencias INT,

    Ativo BOOLEAN DEFAULT TRUE,
    CriadoEm DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_servico_emergencias
    FOREIGN KEY (emergencias_idemergencias)
    REFERENCES emergencias(idemergencias)
    ON DELETE SET NULLON UPDATE CASCADE,

    INDEX idx_servico_nome (NomeServico),
    INDEX idx_servico_ativo (Ativo)
) ENGINE=InnoDB;

CREATE TABLE Profissional_Especialidades (
    Profissional_idProfissional INT NOT NULL,
    Servico_idServico INT NOT NULL,

    PRIMARY KEY (
        Profissional_idProfissional,
        Servico_idServico
    ),

    CONSTRAINT fk_esp_servico
        FOREIGN KEY (Servico_idServico)
        REFERENCES Profissional(idProfissional)
        ON DELETE CASCADE,
    
    CONSTRAINT fk_esp_servico
        FOREIGN KEY (Servico_idServico)
        REFERENCES Servico(idServico)
        ON DELETE CASCADE
) ENGINE=InnoDB
