<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$titulo = 'Login - MPD Nexus';
$erro = '';
$naoConfirmado = false;
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $consulta = $conexao->prepare("SELECT * FROM usuarios WHERE email = ?");
    $consulta->bind_param('s', $email);
    $consulta->execute();
    $resultado = $consulta->get_result();
    $usuario = $resultado->fetch_assoc();

    if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
        if ($smtp['exigir_confirmacao'] && !$usuario['email_confirmado']) {
            $erro = 'Confirme seu e-mail antes de entrar. Procure o link na sua caixa de entrada.';
            $naoConfirmado = true;
        } else {
            session_regenerate_id(true);

            if (!empty($_POST['manter'])) {
                // "Manter conectado": o cookie da sessão passa a valer por 30 dias
                setcookie(session_name(), session_id(), [
                    'expires' => time() + 2592000,
                    'path' => '/',
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]);
            }
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_tipo'] = $usuario['tipo'];

            header('Location: index.php');
            exit;
        }
    } else {
        $erro = 'E-mail ou senha inválidos.';
    }
}

include 'includes/inicio_html.php';
include 'includes/cabecalho.php';
?>

<main class="pagina-formulario">
    <div class="formulario-card">
        <h1>Entrar</h1>
        <p>Acesse sua conta para solicitar serviços.</p>

        <?php if (isset($_GET['senha_alterada'])): ?>
            <div class="mensagem sucesso">Senha alterada! Entre com a nova senha.</div>
        <?php endif; ?>

        <?php if ($erro !== ''): ?>
            <div class="mensagem erro"><?php echo textoSeguro($erro); ?></div>
        <?php endif; ?>

        <?php if ($naoConfirmado): ?>
            <p class="link-formulario"><a href="reenviar.php?email=<?php echo urlencode($email); ?>">Reenviar e-mail de confirmação</a></p>
        <?php endif; ?>

        <form method="post">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" autocomplete="username" value="<?php echo textoSeguro($email); ?>" required>

            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" autocomplete="current-password" required>

            <label class="mostrar-senha"><input type="checkbox" data-mostrar-senha> Mostrar senha</label>
            <label class="mostrar-senha"><input type="checkbox" name="manter" value="1"> Manter conectado</label>

            <button class="botao laranja largura-total" type="submit">Entrar</button>
        </form>

        <p class="link-formulario"><a href="esqueci_senha.php">Esqueci minha senha</a></p>
        <p class="link-formulario">Ainda não tem conta? <a href="cadastro.php">Cadastrar</a></p>
    </div>
</main>

<?php include 'includes/rodape.php'; ?>
