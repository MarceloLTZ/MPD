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
    $nota = intval($_POST['nota'] ?? 5);
    $comentario = trim($_POST['comentario'] ?? '');
    $nota = max(1, min(5, $nota));

    $stmt = $conexao->prepare("INSERT INTO avaliacoes (usuario_id, profissional_id, nota, comentario) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('iiis', $_SESSION['usuario_id'], $id, $nota, $comentario);
    $stmt->execute();

    $stmt = $conexao->prepare("SELECT ROUND(AVG(nota), 1) AS media FROM avaliacoes WHERE profissional_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $media = $stmt->get_result()->fetch_assoc()['media'];

    $stmt = $conexao->prepare("UPDATE profissionais SET nota = ? WHERE id = ?");
    $stmt->bind_param('di', $media, $id);
    $stmt->execute();

    header('Location: profissional.php?id=' . $id);
    exit;
}

$titulo = 'Avaliar - MPD Nexus';
include 'includes/inicio_html.php';
include 'includes/cabecalho.php';
?>

<main class="pagina-formulario">
    <div class="formulario-card">
        <h1>Avaliar profissional</h1>
        <p><?php echo textoSeguro($profissional['nome']); ?></p>

        <form method="post">
            <label for="nota">Nota</label>
            <select id="nota" name="nota">
                <option value="5">5 - Excelente</option>
                <option value="4">4 - Muito bom</option>
                <option value="3">3 - Bom</option>
                <option value="2">2 - Regular</option>
                <option value="1">1 - Ruim</option>
            </select>

            <label for="comentario">Comentário</label>
            <textarea id="comentario" name="comentario" rows="5" placeholder="Conte como foi o serviço"></textarea>

            <button class="botao laranja largura-total" type="submit">Enviar avaliação</button>
        </form>
    </div>
</main>

<?php include 'includes/rodape.php'; ?>
