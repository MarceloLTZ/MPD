<?php
require_once 'conexao.php';
require_once 'funcoes.php';
obrigarLogin();

$id = intval($_GET['id'] ?? 0);
$stmt = $conexao->prepare("SELECT pg.*, s.usuario_id, p.nome AS profissional, sv.nome AS servico FROM pagamentos pg JOIN solicitacoes s ON s.id = pg.solicitacao_id JOIN profissionais p ON p.id = s.profissional_id LEFT JOIN servicos sv ON sv.id = s.servico_id WHERE pg.id = ? AND s.usuario_id = ?");
$stmt->bind_param('ii', $id, $_SESSION['usuario_id']);
$stmt->execute();
$pagamento = $stmt->get_result()->fetch_assoc();

if (!$pagamento) {
    header('Location: meus_servicos.php');
    exit;
}

$titulo = 'Comprovante - MPD Nexus';
include 'includes/inicio_html.php';
include 'includes/cabecalho.php';
?>

<main class="pagina-formulario">
    <div class="comprovante">
        <div class="check">✓</div>
        <h1>Pagamento aprovado</h1>
        <p class="texto-cinza">Comprovante demonstrativo do TCC.</p>

        <div class="linha-comprovante">
            <span>Serviço</span>
            <strong><?php echo textoSeguro($pagamento['servico']); ?></strong>
        </div>
        <div class="linha-comprovante">
            <span>Profissional</span>
            <strong><?php echo textoSeguro($pagamento['profissional']); ?></strong>
        </div>
        <div class="linha-comprovante">
            <span>Método</span>
            <strong><?php echo textoSeguro(strtoupper($pagamento['metodo'])); ?></strong>
        </div>
        <div class="linha-comprovante">
            <span>Valor</span>
            <strong>R$ <?php echo number_format($pagamento['valor'], 2, ',', '.'); ?></strong>
        </div>

        <a class="botao laranja largura-total" href="meus_servicos.php">Voltar aos serviços</a>
    </div>
</main>

<?php include 'includes/rodape.php'; ?>
