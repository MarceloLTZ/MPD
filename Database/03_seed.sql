USE marido_de_aluguel_master;

-- Dados do sistema
INSERT INTO Estado (Nome, Sigla, ddd) VALUES ('São Paulo', 'SP', 11);
INSERT INTO Cidade (Nome, Estado_idEstado) VALUES ('São Paulo', 1);

INSERT INTO emergencias (Tipo, Nivel) VALUES 
('Vazamento Grave', 'Imediato'),
('Curto Circuito', 'Imediato');