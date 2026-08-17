USE marido_de_aluguel_master;


CREATE TABLE Suporte (
    idsuporte INT AUTO_INCREMENT PRIMARY KEY,
    Descricao TEXT NOT NULL,
    Tipo VARCHAR(50) NOT NULL,
    Nivel VARCHAR(30) NOT NUll DEFAULT 'Baixo',
    StatusAtendimento VARCHAR(50) DEFAULT 'Pendente',
    DataAbertura DATETIME DEFAULT Current_TIMESTAMP,
    DataFechamento DATETIME NULL

) ENGINE=InnoDB;

