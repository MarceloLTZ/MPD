SET NAMES utf8mb4;

-- ============================================================
-- SÓ USE ESTE ARQUIVO SE O SEU BANCO JÁ EXISTIA ANTES da confirmação de e-mail.
-- (Banco novo? Ignore: o banco.sql / banco_nuvem.sql já vêm completos.)
-- Rode UMA vez. Se der "Duplicate column", é porque já foi rodado.
-- No banco local, selecione antes o banco mpd_nexus (USE mpd_nexus;).
-- ============================================================
ALTER TABLE usuarios
    ADD COLUMN email_confirmado TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN token_confirmacao VARCHAR(64) NULL,
    ADD COLUMN token_recuperacao VARCHAR(64) NULL,
    ADD COLUMN token_expira DATETIME NULL;

-- Quem já tinha conta antes continua entrando normalmente:
UPDATE usuarios SET email_confirmado = 1;
