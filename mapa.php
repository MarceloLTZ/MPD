<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$profissionais = $conexao->query("SELECT id, nome, especialidade, cidade, nota, latitude, longitude FROM profissionais WHERE ativo = 1 ORDER BY nota DESC");
$titulo = 'Localização - MPD Nexus';
$extraHead = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css">';
$extraScript = '<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
<script src="js/mapa.js"></script>';

include 'includes/inicio_html.php';
include 'includes/cabecalho.php';
?>

<main class="secao">
    <div class="container">
        <p class="destaque">LOCALIZAÇÃO</p>
        <h1>Profissionais próximos</h1>
        <p class="texto-cinza">Veja os profissionais no mapa, use sua localização e trace a rota com distância e tempo.</p>

        <button class="botao laranja" id="usar-localizacao" type="button">Usar minha localização</button>
        <p id="localizacao-texto" class="texto-cinza"></p>

        <div id="mapa" class="mapa-real"></div>

        <div class="lista-localizacao" id="lista-localizacao">
            <?php while ($profissional = $profissionais->fetch_assoc()): ?>
                <div class="localizacao-item"
                     data-id="<?php echo $profissional['id']; ?>"
                     data-nome="<?php echo textoSeguro($profissional['nome']); ?>"
                     data-info="<?php echo textoSeguro($profissional['especialidade'] . ' · ' . $profissional['cidade']); ?>"
                     data-lat="<?php echo $profissional['latitude']; ?>"
                     data-lon="<?php echo $profissional['longitude']; ?>">
                    <div>
                        <h3><?php echo textoSeguro($profissional['nome']); ?></h3>
                        <p><?php echo textoSeguro($profissional['especialidade']); ?> · <?php echo textoSeguro($profissional['cidade']); ?></p>
                    </div>
                    <div>
                        <span class="distancia">Distância não calculada</span>
                        <span class="rota-info"></span>
                        <button type="button" class="botao-rota">Traçar rota</button>
                        <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo $profissional['latitude']; ?>,<?php echo $profissional['longitude']; ?>&travelmode=driving" target="_blank" rel="noopener">Abrir no Google Maps</a>
                        <a href="profissional.php?id=<?php echo $profissional['id']; ?>">Ver perfil</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</main>

<?php include 'includes/rodape.php'; ?>
