-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS loja_virtual;

-- Seleção do banco de dados
USE loja_virtual;

-- Criação da tabela usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    type VARCHAR(1) NOT NULL
);

-- Criação da tabela produtos
CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    preco DECIMAL(10, 2) NOT NULL,
    imagem VARCHAR(255) NOT NULL,
    quantidade INT NOT NULL -- Alterado para INT
);

-- Inserção de dados na tabela produtos
INSERT INTO produtos (nome, preco, imagem, quantidade) VALUES
('Produto 1', 100.00, 'prod1.jpg', 100000000),
('Produto 2', 150.00, 'prod2.jpg', 100000000),
('Produto 3', 200.00, 'prod3.jpg', 100000000);
