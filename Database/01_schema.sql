DROP DATABASE IF EXISTS marido_de_aluguel_master;
CREATE DATABASE marido_de_aluguel_master DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE marido_de_aluguel_master;

-- 1. LOCALIZAÇÃO E ENDEREÇOS
CREATE TABLE Estado ( ... );
CREATE TABLE Cidade ( ... );
CREATE TABLE Endereco ( ... );

-- 2. USUÁRIOS E DEMAIS TABELAS
CREATE TABLE Clientes ( ... );
CREATE TABLE Profissional ( ... );
-- (resto das tabelas CREATE TABLE...)