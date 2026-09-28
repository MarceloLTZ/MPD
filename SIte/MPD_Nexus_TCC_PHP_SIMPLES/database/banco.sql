DROP DATABASE IF EXISTS mpd_nexus;
CREATE DATABASE mpd_nexus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mpd_nexus;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    tipo ENUM('cliente', 'profissional') NOT NULL DEFAULT 'cliente',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE profissionais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    especialidade VARCHAR(100) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    bio VARCHAR(500),
    experiencia_anos INT DEFAULT 1,
    nota DECIMAL(2,1) DEFAULT 5.0,
    valor_visita DECIMAL(10,2) DEFAULT 100.00,
    latitude DECIMAL(10,7),
    longitude DECIMAL(10,7),
    ativo TINYINT(1) DEFAULT 1
);

CREATE TABLE servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    ativo TINYINT(1) DEFAULT 1
);

CREATE TABLE solicitacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    profissional_id INT NOT NULL,
    servico_id INT NOT NULL,
    descricao TEXT NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    data_desejada DATE,
    status ENUM('aguardando', 'aceito', 'em_andamento', 'concluido', 'pago', 'cancelado') DEFAULT 'aguardando',
    valor DECIMAL(10,2) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (profissional_id) REFERENCES profissionais(id),
    FOREIGN KEY (servico_id) REFERENCES servicos(id)
);

CREATE TABLE mensagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    profissional_id INT NOT NULL,
    autor ENUM('cliente', 'profissional') NOT NULL,
    mensagem VARCHAR(1000) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (profissional_id) REFERENCES profissionais(id)
);

CREATE TABLE pagamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    solicitacao_id INT NOT NULL,
    metodo ENUM('pix', 'credito', 'debito') NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    status ENUM('pendente', 'aprovado', 'cancelado') DEFAULT 'pendente',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (solicitacao_id) REFERENCES solicitacoes(id)
);

CREATE TABLE avaliacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    profissional_id INT NOT NULL,
    nota TINYINT NOT NULL,
    comentario VARCHAR(500),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (profissional_id) REFERENCES profissionais(id)
);

INSERT INTO servicos (nome) VALUES
('Eletricista'),
('Encanador'),
('Pintor'),
('Chaveiro'),
('Piscineiro'),
('Jardinagem'),
('Montagem de móveis'),
('Ar-condicionado');

INSERT INTO profissionais
(nome, especialidade, cidade, bio, experiencia_anos, nota, valor_visita, latitude, longitude)
VALUES
('Carlos Mendes', 'Eletricista', 'Mauá', 'Faço instalações, tomadas, iluminação e pequenos reparos elétricos.', 7, 4.9, 120.00, -23.6677250, -46.4613160),
('Mariana Souza', 'Encanador', 'Santo André', 'Trabalho com vazamentos, torneiras, registros e manutenção hidráulica.', 5, 4.8, 110.00, -23.6638790, -46.5324210),
('Rafael Lima', 'Pintor', 'São Bernardo do Campo', 'Pintura interna e externa e preparação de paredes.', 6, 4.7, 150.00, -23.6944910, -46.5654470),
('Ana Costa', 'Jardinagem', 'Ribeirão Pires', 'Faço poda, manutenção de jardins e organização de áreas externas.', 4, 4.9, 100.00, -23.7109810, -46.4130440),
('João Martins', 'Chaveiro', 'Mauá', 'Abertura de portas e troca ou instalação de fechaduras.', 8, 4.8, 90.00, -23.6693400, -46.4578200),
('Felipe Rocha', 'Ar-condicionado', 'Santo André', 'Limpeza, manutenção e instalação residencial de ar-condicionado.', 5, 4.6, 180.00, -23.6534200, -46.5272100);
