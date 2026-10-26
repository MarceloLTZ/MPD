<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$titulo = 'Reenviar confirmação - MPD Nexus';
$email = trim($_GET['email'] ?? '');
$enviado = false;
$envio = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    $stmt = $conexao->prepare("SELECT id, nome FROM usuarios WHERE email = ? AND email_confirmado = 0");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();

    if ($usuario) {
        $envio = enviarConfirmacao($conexao, $usuario['id'], $usuario['nome'], $email);
    }

    // Mesma resposta exista o e-mail ou não (não revela quem tem conta)
    $enviado = true;
}

include 'includes/inicio_html.php';
include 'includes/cabecalho.php';
?>

<main class="pagina-formulario">
    <div class="formulario-card">
        <h1>Reenviar confirmação</h1>

        <?php if ($enviado): ?>
            <div class="mensagem sucesso">Se este e-mail tiver uma conta ainda não confirmada, enviamos um novo link.</div>
            <?php echo avisoModoTeste($envio); ?>
            <p class="link-formulario"><a href="login.php">Voltar para o login</a></p>
        <?php else: ?>
            <p>Digite o e-mail do seu cadastro e enviaremos um novo link.</p>

            <form method="post">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="<?php echo textoSeguro($email); ?>" required>

                <button class="botao laranja largura-total" type="submit">Enviar link</button>
            </form>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/rodape.php'; ?>
