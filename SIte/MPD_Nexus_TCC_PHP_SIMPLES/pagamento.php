<?php
require_once 'conexao.php';
require_once 'funcoes.php';
obrigarLogin();

$id = intval($_GET['id'] ?? 0);
$stmt = $conexao->prepare("SELECT s.*, p.nome AS profissional, sv.nome AS servico FROM solicitacoes s JOIN profissionais p ON p.id = s.profissional_id LEFT JOIN servicos sv ON sv.id = s.servico_id WHERE s.id = ? AND s.usuario_id = ?");
$stmt->bind_param('ii', $id, $_SESSION['usuario_id']);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

if (!$item) {
    header('Location: meus_servicos.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $metodo = $_POST['metodo'] ?? 'pix';
    $permitidos = ['pix', 'credito', 'debito'];

    if (!in_array($metodo, $permitidos)) {
        $metodo = 'pix';
    }

    $stmt = $conexao->prepare("INSERT INTO pagamentos (solicitacao_id, metodo, valor, status) VALUES (?, ?, ?, 'aprovado')");
    $stmt->bind_param('isd', $id, $metodo, $item['valor']);
    $stmt->execute();
    $pagamentoId = $stmt->insert_id;

    $stmt = $conexao->prepare("UPDATE solicitacoes SET status = 'pago' WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    header('Location: comprovante.php?id=' . $pagamentoId);
    exit;
}

$titulo = 'Pagamento - MPD Nexus';
include 'includes/inicio_html.php';
include 'includes/cabecalho.php';
?>

<main class="pagina-formulario">
    <div class="formulario-card largo">
        <h1>Pagamento</h1>
        <p>Esta tela é uma <strong>simulação acadêmica</strong>. Nenhum dado real de cartão é solicitado.</p>

        <div class="resumo-pagamento">
            <p><span>Serviço</span><strong><?php echo textoSeguro($item['servico']); ?></strong></p>
            <p><span>Profissional</span><strong><?php echo textoSeguro($item['profissional']); ?></strong></p>
            <p><span>Total</span><strong>R$ <?php echo number_format($item['valor'], 2, ',', '.'); ?></strong></p>
        </div>

        <form method="post">
            <label for="metodo">Forma de pagamento</label>
            <select id="metodo" name="metodo">
                <option value="pix">PIX</option>
                <option value="credito">Cartão de crédito - simulado</option>
                <option value="debito">Cartão de débito - simulado</option>
            </select>

            <button class="botao laranja largura-total" type="submit">Confirmar pagamento simulado</button>
        </form>
    </div>
</main>

<?php include 'includes/rodape.php'; ?>
