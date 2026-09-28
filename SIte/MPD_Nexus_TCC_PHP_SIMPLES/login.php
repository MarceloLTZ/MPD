<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$titulo = 'Login - MPD Nexus';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $consulta = $conexao->prepare("SELECT * FROM usuarios WHERE email = ?");
    $consulta->bind_param('s', $email);
    $consulta->execute();
    $resultado = $consulta->get_result();
    $usuario = $resultado->fetch_assoc();

    if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_tipo'] = $usuario['tipo'];

        header('Location: index.php');
        exit;
    }

    $erro = 'E-mail ou senha inválidos.';
}

include 'includes/inicio_html.php';
include 'includes/cabecalho.php';
?>

<main class="pagina-formulario">
    <div class="formulario-card">
        <h1>Entrar</h1>
        <p>Acesse sua conta para solicitar serviços.</p>

        <?php if ($erro !== ''): ?>
            <div class="mensagem erro"><?php echo textoSeguro($erro); ?></div>
        <?php endif; ?>

        <form method="post">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required>

            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" required>

            <button class="botao laranja largura-total" type="submit">Entrar</button>
        </form>

        <p class="link-formulario">Ainda não tem conta? <a href="cadastro.php">Cadastrar</a></p>
    </div>
</main>

<?php include 'includes/rodape.php'; ?>
