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
$extraHead = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css">';
$extraScript = '<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
<script src="js/mapa.js"></script>';
include 'includes/inicio_html.php';
include 'includes/cabecalho.php';
?>

<main class="secao">
    <div class="container perfil-profissional">
        <div class="perfil-principal">
            <img class="foto-perfil" src="<?php echo textoSeguro(fotoProfissional($profissional)); ?>" alt="<?php echo textoSeguro($profissional['nome']); ?>" data-fallback="<?php echo textoSeguro(avatarGerado($profissional['nome'])); ?>" onerror="this.onerror=null;this.src=this.dataset.fallback">
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
                <h2>Onde atende</h2>
                <div id="mapa" class="mapa-real pequeno" data-unico="1"></div>
                <div class="localizacao-item" style="display:none"
                     data-id="<?php echo $profissional['id']; ?>"
                     data-nome="<?php echo textoSeguro($profissional['nome']); ?>"
                     data-info="<?php echo textoSeguro($profissional['especialidade'] . ' · ' . $profissional['cidade']); ?>"
                     data-lat="<?php echo $profissional['latitude']; ?>"
                     data-lon="<?php echo $profissional['longitude']; ?>"></div>
                <p><a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo $profissional['latitude']; ?>,<?php echo $profissional['longitude']; ?>&travelmode=driving" target="_blank" rel="noopener">Abrir rota no Google Maps →</a></p>
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
