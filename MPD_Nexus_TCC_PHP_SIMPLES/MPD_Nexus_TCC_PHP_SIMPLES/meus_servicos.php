<?php
require_once 'conexao.php';
require_once 'funcoes.php';
obrigarLogin();

$stmt = $conexao->prepare("SELECT s.*, p.nome AS profissional, p.especialidade, sv.nome AS servico FROM solicitacoes s JOIN profissionais p ON p.id = s.profissional_id LEFT JOIN servicos sv ON sv.id = s.servico_id WHERE s.usuario_id = ? ORDER BY s.criado_em DESC");
$stmt->bind_param('i', $_SESSION['usuario_id']);
$stmt->execute();
$servicos = $stmt->get_result();

$titulo = 'Meus serviços - MPD Nexus';
include 'includes/inicio_html.php';
include 'includes/cabecalho.php';
?>

<main class="secao">
    <div class="container">
        <p class="destaque">HISTÓRICO</p>
        <h1>Meus serviços</h1>

        <div class="lista-servicos">
            <?php if ($servicos->num_rows === 0): ?>
                <div class="painel">
                    <p>Você ainda não solicitou nenhum serviço.</p>
                    <a class="botao laranja" href="profissionais.php">Encontrar profissional</a>
                </div>
            <?php endif; ?>

            <?php while ($item = $servicos->fetch_assoc()): ?>
                <div class="servico-item">
                    <div>
                        <h3><?php echo textoSeguro($item['servico'] ?: $item['especialidade']); ?></h3>
                        <p><?php echo textoSeguro($item['profissional']); ?> · <?php echo textoSeguro($item['endereco']); ?></p>
                        <span class="status"><?php echo textoSeguro($item['status']); ?></span>
                    </div>

                    <div class="servico-acoes">
                        <strong>R$ <?php echo number_format($item['valor'], 2, ',', '.'); ?></strong>
                        <a href="chat.php?id=<?php echo $item['profissional_id']; ?>">Chat</a>

                        <?php if ($item['status'] !== 'pago'): ?>
                            <a href="pagamento.php?id=<?php echo $item['id']; ?>">Pagar</a>
                        <?php else: ?>
                            <a href="feedback.php?id=<?php echo $item['profissional_id']; ?>">Avaliar</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</main>

<?php include 'includes/rodape.php'; ?>
