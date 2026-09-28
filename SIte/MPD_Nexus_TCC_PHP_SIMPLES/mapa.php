<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$profissionais = $conexao->query("SELECT id, nome, especialidade, cidade, nota, latitude, longitude FROM profissionais WHERE ativo = 1 ORDER BY nota DESC");
$titulo = 'Localização - MPD Nexus';

include 'includes/inicio_html.php';
include 'includes/cabecalho.php';
?>

<main class="secao">
    <div class="container">
        <p class="destaque">LOCALIZAÇÃO</p>
        <h1>Profissionais próximos</h1>
        <p class="texto-cinza">O navegador pode usar sua localização para calcular uma distância aproximada.</p>

        <button class="botao laranja" id="usar-localizacao" type="button">Usar minha localização</button>
        <p id="localizacao-texto" class="texto-cinza"></p>

        <div class="mapa-simples">
            <div class="rua rua-1"></div>
            <div class="rua rua-2"></div>
            <span class="pin pin-1">●</span>
            <span class="pin pin-2">●</span>
            <span class="pin pin-3">●</span>
            <p>Representação visual simples</p>
        </div>

        <div class="lista-localizacao" id="lista-localizacao">
            <?php while ($profissional = $profissionais->fetch_assoc()): ?>
                <div class="localizacao-item"
                     data-lat="<?php echo $profissional['latitude']; ?>"
                     data-lon="<?php echo $profissional['longitude']; ?>">
                    <div>
                        <h3><?php echo textoSeguro($profissional['nome']); ?></h3>
                        <p><?php echo textoSeguro($profissional['especialidade']); ?> · <?php echo textoSeguro($profissional['cidade']); ?></p>
                    </div>
                    <div>
                        <span class="distancia">Distância não calculada</span>
                        <a href="profissional.php?id=<?php echo $profissional['id']; ?>">Ver perfil</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</main>

<?php include 'includes/rodape.php'; ?>
