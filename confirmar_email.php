<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$titulo = 'Confirmar e-mail - MPD Nexus';
$sucesso = false;
$token = $_GET['token'] ?? '';

if ($token !== '') {
    $hash = hash('sha256', $token);

    $stmt = $conexao->prepare("SELECT id FROM usuarios WHERE token_confirmacao = ?");
    $stmt->bind_param('s', $hash);
    $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();

    if ($usuario) {
        $stmt = $conexao->prepare("UPDATE usuarios SET email_confirmado = 1, token_confirmacao = NULL WHERE id = ?");
        $stmt->bind_param('i', $usuario['id']);
        $stmt->execute();
        $sucesso = true;
    }
}

include 'includes/inicio_html.php';
include 'includes/cabecalho.php';
?>

<main class="pagina-formulario">
    <div class="formulario-card">
        <?php if ($sucesso): ?>
            <h1>E-mail confirmado!</h1>
            <div class="mensagem sucesso">Sua conta está ativa. Já pode entrar.</div>
            <a class="botao laranja largura-total" href="login.php">Entrar</a>
        <?php else: ?>
            <h1>Link inválido</h1>
            <div class="mensagem erro">Este link de confirmação não é válido ou já foi usado.</div>
            <p class="link-formulario"><a href="reenviar.php">Receber um novo link</a></p>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/rodape.php'; ?>
