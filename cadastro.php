<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$titulo = 'Cadastro - MPD Nexus';
$erro = '';
$envio = null;
$cadastrado = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';
    $tipo = $_POST['tipo'] ?? 'cliente';

    if (!in_array($tipo, ['cliente', 'profissional'])) {
        $tipo = 'cliente';
    }

    if ($nome === '' || $email === '' || strlen($senha) < 8) {
        $erro = 'Preencha os campos e use uma senha com pelo menos 8 caracteres.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Digite um e-mail válido.';
    } elseif ($senha !== $confirmarSenha) {
        $erro = 'As senhas não são iguais.';
    } else {
        $consulta = $conexao->prepare("SELECT id FROM usuarios WHERE email = ?");
        $consulta->bind_param('s', $email);
        $consulta->execute();
        $resultado = $consulta->get_result();

        if ($resultado->num_rows > 0) {
            $erro = 'Este e-mail já está cadastrado.';
        } else {
            // A senha NUNCA é guardada como texto: só o "hash" dela vai para o banco
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $confirmado = $smtp['exigir_confirmacao'] ? 0 : 1;

            $cadastro = $conexao->prepare("INSERT INTO usuarios (nome, email, senha_hash, tipo, email_confirmado) VALUES (?, ?, ?, ?, ?)");
            $cadastro->bind_param('ssssi', $nome, $email, $senhaHash, $tipo, $confirmado);
            $cadastro->execute();
            $novoId = $cadastro->insert_id;

            if ($smtp['exigir_confirmacao']) {
                // Conta criada, mas só entra depois de confirmar o e-mail
                $envio = enviarConfirmacao($conexao, $novoId, $nome, $email);
                $cadastrado = true;
            } else {
                $_SESSION['usuario_id'] = $novoId;
                $_SESSION['usuario_nome'] = $nome;
                $_SESSION['usuario_tipo'] = $tipo;

                header('Location: index.php');
                exit;
            }
        }
    }
}

include 'includes/inicio_html.php';
include 'includes/cabecalho.php';
?>

<main class="pagina-formulario">
    <div class="formulario-card">
        <?php if ($cadastrado): ?>
            <h1>Confirme seu e-mail</h1>

            <?php if ($envio['enviado']): ?>
                <div class="mensagem sucesso">Enviamos um link de confirmação para <strong><?php echo textoSeguro($email); ?></strong>. Confira também a caixa de spam.</div>
            <?php elseif (!emailEmModoTeste()): ?>
                <div class="mensagem erro">Sua conta foi criada, mas não conseguimos enviar o e-mail agora. Tente <a href="reenviar.php?email=<?php echo urlencode($email); ?>"><u>reenviar o link</u></a>.</div>
            <?php endif; ?>

            <?php echo avisoModoTeste($envio); ?>

            <p class="link-formulario">Já confirmou? <a href="login.php">Entrar</a></p>
        <?php else: ?>
            <h1>Criar conta</h1>
            <p>Cadastro simples para utilizar a plataforma.</p>

            <?php if ($erro !== ''): ?>
                <div class="mensagem erro"><?php echo textoSeguro($erro); ?></div>
            <?php endif; ?>

            <form method="post" id="form-cadastro">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" autocomplete="name" value="<?php echo textoSeguro($_POST['nome'] ?? ''); ?>" required>

                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" autocomplete="email" value="<?php echo textoSeguro($_POST['email'] ?? ''); ?>" required>

                <label for="senha">Senha (mínimo 8 caracteres)</label>
                <input type="password" id="senha" name="senha" minlength="8" autocomplete="new-password" required>

                <label for="confirmar_senha">Confirmar senha</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" minlength="8" autocomplete="new-password" required>

                <label class="mostrar-senha"><input type="checkbox" data-mostrar-senha> Mostrar senha</label>

                <label for="tipo">Tipo de conta</label>
                <select id="tipo" name="tipo">
                    <option value="cliente">Cliente</option>
                    <option value="profissional">Profissional</option>
                </select>

                <button class="botao laranja largura-total" type="submit">Cadastrar</button>
            </form>

            <p class="link-formulario">Já tem conta? <a href="login.php">Entrar</a></p>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/rodape.php'; ?>
