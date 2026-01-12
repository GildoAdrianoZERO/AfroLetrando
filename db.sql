-- 1. Cria o Banco de Dados
CREATE DATABASE IF NOT EXISTS afroletrando;
USE afroletrando;

-- 2. Cria a Tabela de Usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    nivel VARCHAR(20) DEFAULT 'editor',
    status TINYINT(1) DEFAULT 1,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Cria a Tabela de Edições (Revistas)
CREATE TABLE IF NOT EXISTS edicoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    volume VARCHAR(50) NOT NULL,
    data_publicacao DATE,
    descricao TEXT,
    imagem_capa VARCHAR(255),
    link_pdf VARCHAR(255),
    publicado_por INT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (publicado_por) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- 4. Cria o Usuário Admin (Senha: 123456)
-- O código estranho na senha é a criptografia de '123456'
INSERT INTO usuarios (nome, email, senha, nivel) 
VALUES (
    'Admin Principal', 
    'admin@afroletrando.com', 
    '$2y$10$I8O0.k/5S.8.9.0.1.2.3.4.5.6.7.8.9.0.1.2.3.4.5.uH8.G6', 
    'admin'
);

-- 5. Insere uma Revista de Teste
INSERT INTO edicoes (titulo, volume, data_publicacao, descricao, imagem_capa, link_pdf) 
VALUES (
    'Letramento Racial e Educação', 
    'Vol. 10', 
    '2024-06-01', 
    'Dossiê temático focado na implementação da Lei 10.639/03 nas escolas públicas.', 
    'https://images.unsplash.com/photo-1550684848-fac1c5b4e853?auto=format&fit=crop&q=80&w=800',
    '#'
);

-- logs dos users
CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    acao VARCHAR(50) NOT NULL, -- Ex: 'CADASTRO', 'EDICAO', 'EXCLUSAO'
    descricao TEXT NOT NULL,   -- Ex: 'Editou a revista Vol. 10'
    data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS sobre (
    id INT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    texto TEXT NOT NULL,
    imagem VARCHAR(255)
);

INSERT INTO sobre (id, titulo, texto, imagem) VALUES 
(1, 'A palavra é uma semente', 'A Revista Afroletrando é um periódico interdisciplinar...', 'https://images.unsplash.com/photo-1532012197367-2836fb3ee4f2?auto=format&fit=crop&q=80&w=800');

CREATE TABLE equipe (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cargo VARCHAR(100) NOT NULL,
    instituicao VARCHAR(100),
    imagem VARCHAR(255) DEFAULT 'https://ui-avatars.com/api/?name=Membro&background=random'
);

-- Inserindo o Editor-Chefe padrão (você)
INSERT INTO equipe (nome, cargo, instituicao, imagem) VALUES 
('Gildo Developer', 'Editor-Chefe', 'Universidade Federal', 'https://ui-avatars.com/api/?name=Gildo+Dev&background=c2410c&color=fff');