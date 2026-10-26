<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$id = intval($_GET['id'] ?? 0);

$stmt = $conexao->prepare("SELECT * FROM profissionais WHERE id = ? AND ativo = 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$profissional = $stmt->get_result()->fetch_assoc();

if (!$profissional) {
    header('Location: profissionais.php');
    exit;
}

$stmt = $conexao->prepare("SELECT a.*, u.nome AS cliente FROM avaliacoes a JOIN usuarios u ON u.id = a.usuario_id WHERE a.profissional_id = ? ORDER BY a.criado_em DESC LIMIT 5");
$stmt->bind_param('i', $id);
$stmt->execute();
$avaliacoes = $stmt->get_result();

$titulo = $profissional['nome'] . ' - MPD Nexus';
include 'includes/inicio_html.php';
include 'includes/cabecalho.php';
?>

<main class="secao">
    <div class="container perfil-profissional">
        <div class="perfil-principal">
            <div class="avatar grande"><?php echo strtoupper(substr($profissional['nome'], 0, 1)); ?></div>
            <div>
                <p class="destaque"><?php echo textoSeguro($profissional['especialidade']); ?></p>
                <h1><?php echo textoSeguro($profissional['nome']); ?></h1>
                <p>★ <?php echo textoSeguro($profissional['nota']); ?> / 5</p>
                <p class="texto-cinza">📍 <?php echo textoSeguro($profissional['cidade']); ?></p>
            </div>
        </div>

        <div class="perfil-grid">
            <div class="painel">
                <h2>Sobre</h2>
                <p><?php echo textoSeguro($profissional['bio']); ?></p>
                <p><strong>Experiência:</strong> <?php echo intval($profissional['experiencia_anos']); ?> anos</p>
                <p><strong>Valor da visita:</strong> R$ <?php echo number_format($profissional['valor_visita'], 2, ',', '.'); ?></p>

                <div class="acoes-perfil">
                    <a class="botao laranja" href="solicitar.php?id=<?php echo $id; ?>">Solicitar serviço</a>
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <a class="botao escuro" href="chat.php?id=<?php echo $id; ?>">Abrir chat</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="painel">
                <h2>Avaliações</h2>
                <?php if ($avaliacoes->num_rows === 0): ?>
                    <p class="texto-cinza">Ainda não existem avaliações.</p>
                <?php endif; ?>

                <?php while ($avaliacao = $avaliacoes->fetch_assoc()): ?>
                    <div class="avaliacao">
                        <strong><?php echo textoSeguro($avaliacao['cliente']); ?></strong>
                        <span>★ <?php echo intval($avaliacao['nota']); ?></span>
                        <p><?php echo textoSeguro($avaliacao['comentario']); ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/rodape.php'; ?>
