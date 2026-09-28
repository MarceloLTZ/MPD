<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$titulo = 'Profissionais - MPD Nexus';
$q = trim($_GET['q'] ?? '');
$especialidade = trim($_GET['especialidade'] ?? '');

if ($q !== '' && $especialidade !== '') {
    $sql = "SELECT * FROM profissionais
            WHERE ativo = 1
            AND (nome LIKE ? OR especialidade LIKE ? OR cidade LIKE ?)
            AND especialidade LIKE ?
            ORDER BY nota DESC, nome";

    $busca = '%' . $q . '%';
    $filtroEspecialidade = '%' . $especialidade . '%';

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param('ssss', $busca, $busca, $busca, $filtroEspecialidade);
} elseif ($q !== '') {
    $sql = "SELECT * FROM profissionais
            WHERE ativo = 1
            AND (nome LIKE ? OR especialidade LIKE ? OR cidade LIKE ?)
            ORDER BY nota DESC, nome";

    $busca = '%' . $q . '%';

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param('sss', $busca, $busca, $busca);
} elseif ($especialidade !== '') {
    $sql = "SELECT * FROM profissionais
            WHERE ativo = 1
            AND especialidade LIKE ?
            ORDER BY nota DESC, nome";

    $filtroEspecialidade = '%' . $especialidade . '%';

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param('s', $filtroEspecialidade);
} else {
    $stmt = $conexao->prepare("SELECT * FROM profissionais WHERE ativo = 1 ORDER BY nota DESC, nome");
}

$stmt->execute();
$profissionais = $stmt->get_result();

include 'includes/inicio_html.php';
include 'includes/cabecalho.php';
?>

<main class="secao">
    <div class="container">
        <p class="destaque">BUSCAR</p>
        <h1>Profissionais</h1>
        <p class="texto-cinza">Pesquise por nome, serviço ou cidade.</p>

        <form class="filtros" method="get">
            <input type="text" name="q" placeholder="Ex.: eletricista" value="<?php echo textoSeguro($q); ?>">
            <input type="text" name="especialidade" placeholder="Especialidade" value="<?php echo textoSeguro($especialidade); ?>">
            <button class="botao laranja" type="submit">Pesquisar</button>
        </form>

        <div class="grade-profissionais espaco-topo">
            <?php if ($profissionais->num_rows === 0): ?>
                <p>Nenhum profissional encontrado.</p>
            <?php endif; ?>

            <?php while ($profissional = $profissionais->fetch_assoc()): ?>
                <div class="card-profissional">
                    <div class="perfil-linha">
                        <div class="avatar"><?php echo strtoupper(substr($profissional['nome'], 0, 1)); ?></div>
                        <div>
                            <h3><?php echo textoSeguro($profissional['nome']); ?></h3>
                            <p><?php echo textoSeguro($profissional['especialidade']); ?></p>
                        </div>
                    </div>

                    <p class="nota">★ <?php echo textoSeguro($profissional['nota']); ?> / 5</p>
                    <p class="descricao-card"><?php echo textoSeguro($profissional['bio']); ?></p>
                    <p class="cidade">📍 <?php echo textoSeguro($profissional['cidade']); ?></p>

                    <div class="card-rodape">
                        <strong>R$ <?php echo number_format($profissional['valor_visita'], 2, ',', '.'); ?></strong>
                        <a class="botao-pequeno" href="profissional.php?id=<?php echo $profissional['id']; ?>">Ver perfil</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</main>

<?php include 'includes/rodape.php'; ?>
