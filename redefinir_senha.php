<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$titulo = 'Nova senha - MPD Nexus';
$erro = '';
$token = $_GET['token'] ?? ($_POST['token'] ?? '');
$hash = hash('sha256', $token);

// O link só vale se o token existir e ainda estiver dentro de 1 hora
$stmt = $conexao->prepare("SELECT id FROM usuarios WHERE token_recuperacao = ? AND token_expira > NOW()");
$stmt->bind_param('s', $hash);
$stmt->execute();
$usuario = $token !== '' ? $stmt->get_result()->fetch_assoc() : null;

if ($usuario && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $senha = $_POST['senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    if (strlen($senha) < 8) {
        $erro = 'Use uma senha com pelo menos 8 caracteres.';
    } elseif ($senha !== $confirmarSenha) {
        $erro = 'As senhas não são iguais.';
    } else {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        // Quem recebeu o link no e-mail provou que é o dono dele, então já fica confirmado
        $stmt = $conexao->prepare("UPDATE usuarios SET senha_hash = ?, email_confirmado = 1, token_recuperacao = NULL, token_expira = NULL WHERE id = ?");
        $stmt->bind_param('si', $senhaHash, $usuario['id']);
        $stmt->execute();

        header('Location: login.php?senha_alterada=1');
        exit;
    }
}

include 'includes/inicio_html.php';
include 'includes/cabecalho.php';
?>

<main class="pagina-formulario">
    <div class="formulario-card">
        <?php if (!$usuario): ?>
            <h1>Link inválido</h1>
            <div class="mensagem erro">Este link não é válido ou já expirou.</div>
            <p class="link-formulario"><a href="esqueci_senha.php">Pedir um novo link</a></p>
        <?php else: ?>
            <h1>Criar nova senha</h1>

            <?php if ($erro !== ''): ?>
                <div class="mensagem erro"><?php echo textoSeguro($erro); ?></div>
            <?php endif; ?>

            <form method="post" id="form-cadastro">
                <input type="hidden" name="token" value="<?php echo textoSeguro($token); ?>">

                <label for="senha">Nova senha (mínimo 8 caracteres)</label>
                <input type="password" id="senha" name="senha" minlength="8" autocomplete="new-password" required>

                <label for="confirmar_senha">Confirmar nova senha</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" minlength="8" autocomplete="new-password" required>

                <label class="mostrar-senha"><input type="checkbox" data-mostrar-senha> Mostrar senha</label>

                <button class="botao laranja largura-total" type="submit">Salvar nova senha</button>
            </form>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/rodape.php'; ?>
