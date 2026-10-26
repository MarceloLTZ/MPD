<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$titulo = 'Esqueci minha senha - MPD Nexus';
$enviado = false;
$envio = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    $stmt = $conexao->prepare("SELECT id, nome FROM usuarios WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();

    if ($usuario) {
        $envio = enviarRecuperacao($conexao, $usuario['id'], $usuario['nome'], $email);
    }

    // Mesma resposta exista o e-mail ou não (não revela quem tem conta)
    $enviado = true;
}

include 'includes/inicio_html.php';
include 'includes/cabecalho.php';
?>

<main class="pagina-formulario">
    <div class="formulario-card">
        <h1>Esqueci minha senha</h1>

        <?php if ($enviado): ?>
            <div class="mensagem sucesso">Se este e-mail tiver cadastro, enviamos um link para criar uma nova senha. Ele vale por 1 hora.</div>
            <?php echo avisoModoTeste($envio); ?>
            <p class="link-formulario"><a href="login.php">Voltar para o login</a></p>
        <?php else: ?>
            <p>Digite seu e-mail e enviaremos um link para criar uma nova senha.</p>

            <form method="post">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" autocomplete="email" required>

                <button class="botao laranja largura-total" type="submit">Enviar link</button>
            </form>

            <p class="link-formulario"><a href="login.php">Voltar para o login</a></p>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/rodape.php'; ?>
