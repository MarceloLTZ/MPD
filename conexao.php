<?php
// Sessão que dura até 30 dias quando a pessoa marca "Manter conectado"
ini_set('session.gc_maxlifetime', 2592000);
session_start();

require_once __DIR__ . '/config.php';

// Conecta no MySQL (local ou na nuvem, conforme o config.php)
$conexao = mysqli_init();

if ($config['ssl']) {
    // Bancos na nuvem exigem conexão segura (SSL)
    $conexao->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);
    $conexao->ssl_set(null, null, null, null, null);
    $flags = MYSQLI_CLIENT_SSL;
} else {
    $flags = 0;
}

mysqli_report(MYSQLI_REPORT_OFF);

if (!@$conexao->real_connect($config['host'], $config['usuario'], $config['senha'], $config['banco'], (int) $config['porta'], null, $flags)) {
    die("Erro ao conectar com o banco de dados: " . $conexao->connect_error);
}

$conexao->set_charset("utf8mb4");
?>
