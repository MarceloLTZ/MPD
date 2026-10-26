SET NAMES utf8mb4;

-- ============================================================
-- BANCO NA NUVEM (Aiven, TiDB Cloud, Clever Cloud...)
-- O provedor já criou o banco para você, então aqui só criamos as tabelas.
-- Pode ser importado quantas vezes quiser: NÃO apaga nada do que já existe.
-- ============================================================

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    tipo ENUM('cliente', 'profissional') NOT NULL DEFAULT 'cliente',
    email_confirmado TINYINT(1) NOT NULL DEFAULT 0,
    token_confirmacao VARCHAR(64) NULL,
    token_recuperacao VARCHAR(64) NULL,
    token_expira DATETIME NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS profissionais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL UNIQUE,
    foto_url VARCHAR(500),
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

CREATE TABLE IF NOT EXISTS servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    imagem_url VARCHAR(500),
    ativo TINYINT(1) DEFAULT 1
);

CREATE TABLE IF NOT EXISTS solicitacoes (
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

CREATE TABLE IF NOT EXISTS mensagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    profissional_id INT NOT NULL,
    autor ENUM('cliente', 'profissional') NOT NULL,
    mensagem VARCHAR(1000) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (profissional_id) REFERENCES profissionais(id)
);

CREATE TABLE IF NOT EXISTS pagamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    solicitacao_id INT NOT NULL,
    metodo ENUM('pix', 'credito', 'debito') NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    status ENUM('pendente', 'aprovado', 'cancelado') DEFAULT 'pendente',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (solicitacao_id) REFERENCES solicitacoes(id)
);

CREATE TABLE IF NOT EXISTS avaliacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    profissional_id INT NOT NULL,
    nota TINYINT NOT NULL,
    comentario VARCHAR(500),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (profissional_id) REFERENCES profissionais(id)
);

INSERT IGNORE INTO servicos (nome, imagem_url) VALUES
('Eletricista',        'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=600&q=70'),
('Encanador',          'https://images.unsplash.com/photo-1585704032915-c3400ca199e7?auto=format&fit=crop&w=600&q=70'),
('Pintor',             'https://images.unsplash.com/photo-1562259949-e8e7689d7828?auto=format&fit=crop&w=600&q=70'),
('Chaveiro',           'https://images.unsplash.com/photo-1558002038-1055907df827?auto=format&fit=crop&w=600&q=70'),
('Piscineiro',         'https://images.unsplash.com/photo-1575429198097-0414ec08e8cd?auto=format&fit=crop&w=600&q=70'),
('Jardinagem',         'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?auto=format&fit=crop&w=600&q=70'),
('Montagem de móveis', 'https://images.unsplash.com/photo-1581141849291-1125c7b692b5?auto=format&fit=crop&w=600&q=70'),
('Ar-condicionado',    'https://images.unsplash.com/photo-1631545806609-5a1a1e3d8f0b?auto=format&fit=crop&w=600&q=70');

INSERT IGNORE INTO profissionais
(nome, foto_url, especialidade, cidade, bio, experiencia_anos, nota, valor_visita, latitude, longitude)
VALUES
('Carlos Mendes', 'https://i.pravatar.cc/300?img=12', 'Eletricista', 'Mauá', 'Faço instalações, tomadas, iluminação e pequenos reparos elétricos.', 7, 4.9, 120.00, -23.6677250, -46.4613160),
('Mariana Souza', 'https://i.pravatar.cc/300?img=47', 'Encanador', 'Santo André', 'Trabalho com vazamentos, torneiras, registros e manutenção hidráulica.', 5, 4.8, 110.00, -23.6638790, -46.5324210),
('Rafael Lima', 'https://i.pravatar.cc/300?img=33', 'Pintor', 'São Bernardo do Campo', 'Pintura interna e externa e preparação de paredes.', 6, 4.7, 150.00, -23.6944910, -46.5654470),
('Ana Costa', 'https://i.pravatar.cc/300?img=45', 'Jardinagem', 'Ribeirão Pires', 'Faço poda, manutenção de jardins e organização de áreas externas.', 4, 4.9, 100.00, -23.7109810, -46.4130440),
('João Martins', 'https://i.pravatar.cc/300?img=15', 'Chaveiro', 'Mauá', 'Abertura de portas e troca ou instalação de fechaduras.', 8, 4.8, 90.00, -23.6693400, -46.4578200),
('Felipe Rocha', 'https://i.pravatar.cc/300?img=60', 'Ar-condicionado', 'Santo André', 'Limpeza, manutenção e instalação residencial de ar-condicionado.', 5, 4.6, 180.00, -23.6534200, -46.5272100);
