<?php
require_once 'conexao.php';
require_once 'funcoes.php';
obrigarLogin();

$id = intval($_GET['id'] ?? 0);
$stmt = $conexao->prepare("SELECT * FROM profissionais WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$profissional = $stmt->get_result()->fetch_assoc();

if (!$profissional) {
    header('Location: profissionais.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensagem = trim($_POST['mensagem'] ?? '');

    if ($mensagem !== '') {
        $stmt = $conexao->prepare("INSERT INTO mensagens (usuario_id, profissional_id, autor, mensagem) VALUES (?, ?, 'cliente', ?)");
        $stmt->bind_param('iis', $_SESSION['usuario_id'], $id, $mensagem);
        $stmt->execute();

        // Resposta automática apenas para demonstrar o chat no TCC.
        $resposta = 'Olá! Recebi sua mensagem. Podemos combinar os detalhes do serviço por aqui.';
        $stmt = $conexao->prepare("INSERT INTO mensagens (usuario_id, profissional_id, autor, mensagem) VALUES (?, ?, 'profissional', ?)");
        $stmt->bind_param('iis', $_SESSION['usuario_id'], $id, $resposta);
        $stmt->execute();
    }

    header('Location: chat.php?id=' . $id);
    exit;
}

$stmt = $conexao->prepare("SELECT * FROM mensagens WHERE usuario_id = ? AND profissional_id = ? ORDER BY criado_em, id");
$stmt->bind_param('ii', $_SESSION['usuario_id'], $id);
$stmt->execute();
$mensagens = $stmt->get_result();

$titulo = 'Chat - MPD Nexus';
include 'includes/inicio_html.php';
include 'includes/cabecalho.php';
?>

<main class="chat-pagina">
    <div class="chat-caixa">
        <div class="chat-topo">
            <div class="avatar"><?php echo strtoupper(substr($profissional['nome'], 0, 1)); ?></div>
            <div>
                <h2><?php echo textoSeguro($profissional['nome']); ?></h2>
                <p><?php echo textoSeguro($profissional['especialidade']); ?></p>
            </div>
        </div>

        <div class="mensagens">
            <?php if ($mensagens->num_rows === 0): ?>
                <p class="texto-cinza">Nenhuma mensagem. Escreva para o profissional.</p>
            <?php endif; ?>

            <?php while ($mensagem = $mensagens->fetch_assoc()): ?>
                <div class="balao <?php echo $mensagem['autor'] === 'cliente' ? 'minha' : 'dele'; ?>">
                    <p><?php echo textoSeguro($mensagem['mensagem']); ?></p>
                    <small><?php echo date('H:i', strtotime($mensagem['criado_em'])); ?></small>
                </div>
            <?php endwhile; ?>
        </div>

        <form class="enviar-mensagem" method="post">
            <input type="text" name="mensagem" placeholder="Digite uma mensagem" autocomplete="off" required>
            <button type="submit">Enviar</button>
        </form>
    </div>
</main>

<?php include 'includes/rodape.php'; ?>
