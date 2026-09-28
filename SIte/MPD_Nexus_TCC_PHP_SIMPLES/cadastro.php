<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$titulo = 'Cadastro - MPD Nexus';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $tipo = $_POST['tipo'] ?? 'cliente';

    if ($nome === '' || $email === '' || strlen($senha) < 6) {
        $erro = 'Preencha os campos e use uma senha com pelo menos 6 caracteres.';
    } else {
        $consulta = $conexao->prepare("SELECT id FROM usuarios WHERE email = ?");
        $consulta->bind_param('s', $email);
        $consulta->execute();
        $resultado = $consulta->get_result();

        if ($resultado->num_rows > 0) {
            $erro = 'Este e-mail já está cadastrado.';
        } else {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $cadastro = $conexao->prepare("INSERT INTO usuarios (nome, email, senha_hash, tipo) VALUES (?, ?, ?, ?)");
            $cadastro->bind_param('ssss', $nome, $email, $senhaHash, $tipo);
            $cadastro->execute();

            $_SESSION['usuario_id'] = $cadastro->insert_id;
            $_SESSION['usuario_nome'] = $nome;
            $_SESSION['usuario_tipo'] = $tipo;

            header('Location: index.php');
            exit;
        }
    }
}

include 'includes/inicio_html.php';
include 'includes/cabecalho.php';
?>

<main class="pagina-formulario">
    <div class="formulario-card">
        <h1>Criar conta</h1>
        <p>Cadastro simples para utilizar a plataforma.</p>

        <?php if ($erro !== ''): ?>
            <div class="mensagem erro"><?php echo textoSeguro($erro); ?></div>
        <?php endif; ?>

        <form method="post">
            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome" required>

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required>

            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" minlength="6" required>

            <label for="tipo">Tipo de conta</label>
            <select id="tipo" name="tipo">
                <option value="cliente">Cliente</option>
                <option value="profissional">Profissional</option>
            </select>

            <button class="botao laranja largura-total" type="submit">Cadastrar</button>
        </form>

        <p class="link-formulario">Já tem conta? <a href="login.php">Entrar</a></p>
    </div>
</main>

<?php include 'includes/rodape.php'; ?>
