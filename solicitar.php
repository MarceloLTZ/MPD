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

$servicos = $conexao->query("SELECT * FROM servicos WHERE ativo = 1 ORDER BY nome");
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servicoId = intval($_POST['servico_id'] ?? 0);
    $descricao = trim($_POST['descricao'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');
    $data = $_POST['data_desejada'] ?? null;
    $valor = $profissional['valor_visita'];

    if ($descricao === '' || $endereco === '') {
        $erro = 'Preencha a descrição e o endereço.';
    } else {
        $stmt = $conexao->prepare("INSERT INTO solicitacoes (usuario_id, profissional_id, servico_id, descricao, endereco, data_desejada, status, valor) VALUES (?, ?, ?, ?, ?, ?, 'aguardando', ?)");
        $stmt->bind_param('iiisssd', $_SESSION['usuario_id'], $id, $servicoId, $descricao, $endereco, $data, $valor);
        $stmt->execute();

        header('Location: meus_servicos.php');
        exit;
    }
}

$titulo = 'Solicitar serviço - MPD Nexus';
include 'includes/inicio_html.php';
include 'includes/cabecalho.php';
?>

<main class="pagina-formulario">
    <div class="formulario-card largo">
        <h1>Solicitar serviço</h1>
        <p>Profissional: <strong><?php echo textoSeguro($profissional['nome']); ?></strong></p>

        <?php if ($erro !== ''): ?>
            <div class="mensagem erro"><?php echo textoSeguro($erro); ?></div>
        <?php endif; ?>

        <form method="post">
            <label for="servico_id">Serviço</label>
            <select id="servico_id" name="servico_id" required>
                <?php while ($servico = $servicos->fetch_assoc()): ?>
                    <option value="<?php echo $servico['id']; ?>"><?php echo textoSeguro($servico['nome']); ?></option>
                <?php endwhile; ?>
            </select>

            <label for="descricao">O que precisa ser feito?</label>
            <textarea id="descricao" name="descricao" rows="5" required></textarea>

            <label for="endereco">Endereço</label>
            <input type="text" id="endereco" name="endereco" required>

            <label for="data_desejada">Data desejada</label>
            <input type="date" id="data_desejada" name="data_desejada">

            <p class="resumo-valor">Valor inicial da visita: <strong>R$ <?php echo number_format($profissional['valor_visita'], 2, ',', '.'); ?></strong></p>

            <button class="botao laranja largura-total" type="submit">Enviar solicitação</button>
        </form>
    </div>
</main>

<?php include 'includes/rodape.php'; ?>
