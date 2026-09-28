<?php
session_start();

$servidor = "localhost";
$usuario = "root";
$senha = "123";
$banco = "mpd_nexus";

$conexao = new mysqli($servidor, $usuario, $senha, $banco);

if ($conexao->connect_error) {
    die("Erro ao conectar com o banco de dados: " . $conexao->connect_error);
}

$conexao->set_charset("utf8mb4");
?>
