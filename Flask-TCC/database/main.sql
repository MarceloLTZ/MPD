DROP DATABASE IF EXISTS mpd_nexus;
CREATE DATABASE mpd_nexus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mpd_nexus;

CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  tipo ENUM('cliente','profissional') NOT NULL DEFAULT 'cliente',
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
  latitude DECIMAL(10,7),
  longitude DECIMAL(10,7),
  ativo TINYINT(1) DEFAULT 1
);

CREATE TABLE servicos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  icone VARCHAR(80) DEFAULT 'bi-tools',
  ativo TINYINT(1) DEFAULT 1
);

CREATE TABLE solicitacoes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  profissional_id INT NOT NULL,
  servico_id INT NULL,
  descricao TEXT NOT NULL,
  endereco VARCHAR(255) NOT NULL,
  data_desejada DATE NULL,
  status ENUM('aguardando','aceito','em_andamento','concluido','pago','cancelado') DEFAULT 'aguardando',
  valor DECIMAL(10,2) NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_solicitacao_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
  CONSTRAINT fk_solicitacao_profissional FOREIGN KEY (profissional_id) REFERENCES profissionais(id),
  CONSTRAINT fk_solicitacao_servico FOREIGN KEY (servico_id) REFERENCES servicos(id)
);

CREATE TABLE mensagens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  profissional_id INT NOT NULL,
  autor ENUM('cliente','profissional') NOT NULL,
  mensagem VARCHAR(1000) NOT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_mensagem_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
  CONSTRAINT fk_mensagem_profissional FOREIGN KEY (profissional_id) REFERENCES profissionais(id)
);

CREATE TABLE pagamentos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  solicitacao_id INT NOT NULL,
  metodo ENUM('pix','credito','debito') NOT NULL,
  valor DECIMAL(10,2) NOT NULL,
  status ENUM('pendente','aprovado','cancelado') DEFAULT 'pendente',
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pagamento_solicitacao FOREIGN KEY (solicitacao_id) REFERENCES solicitacoes(id)
);

CREATE TABLE avaliacoes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  profissional_id INT NOT NULL,
  nota TINYINT NOT NULL,
  comentario VARCHAR(500),
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_avaliacao_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
  CONSTRAINT fk_avaliacao_profissional FOREIGN KEY (profissional_id) REFERENCES profissionais(id)
);

INSERT INTO servicos (nome, icone) VALUES
('Eletricista','bi-lightning-charge'),
('Encanador','bi-droplet'),
('Pintor','bi-brush'),
('Chaveiro','bi-key'),
('Piscineiro','bi-water'),
('Jardinagem','bi-flower1'),
('Montagem','bi-hammer'),
('Ar-condicionado','bi-snow');

INSERT INTO profissionais (nome,especialidade,cidade,bio,experiencia_anos,nota,latitude,longitude) VALUES
('Carlos Mendes','Eletricista','Mauá','Instalações, tomadas, iluminação e pequenos reparos elétricos residenciais.',7,4.9,-23.667725,-46.461316),
('Mariana Souza','Encanador','Santo André','Manutenção hidráulica, vazamentos, torneiras, registros e limpeza preventiva.',5,4.8,-23.663879,-46.532421),
('Rafael Lima','Pintor','São Bernardo do Campo','Pintura interna e externa, preparação de paredes e pequenos acabamentos.',6,4.7,-23.694491,-46.565447),
('Ana Costa','Jardinagem','Ribeirão Pires','Cuidados com jardins, poda, manutenção e organização de áreas externas.',4,4.9,-23.710981,-46.413044),
('João Martins','Chaveiro','Mauá','Abertura de portas, troca de fechaduras e instalação de fechaduras digitais.',8,4.8,-23.669340,-46.457820),
('Felipe Rocha','Ar-condicionado','Santo André','Limpeza, manutenção preventiva e instalação residencial de ar-condicionado.',5,4.6,-23.653420,-46.527210);
