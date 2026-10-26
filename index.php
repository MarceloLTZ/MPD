<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$titulo = 'MPD Nexus - Início';

$servicos = $conexao->query("SELECT * FROM servicos WHERE ativo = 1 ORDER BY nome LIMIT 8");
$profissionais = $conexao->query("SELECT * FROM profissionais WHERE ativo = 1 ORDER BY nota DESC LIMIT 4");

include 'includes/inicio_html.php';
include 'includes/cabecalho.php';
?>

<main>
    <section class="hero">
        <div class="container hero-conteudo">
            <div class="hero-texto">
                <p class="destaque">MANUTENÇÃO RESIDENCIAL</p>
                <h1>Sua casa sempre segura e funcionando.</h1>
                <p class="hero-descricao">
                    Encontre profissionais para pequenos reparos, manutenção e serviços residenciais.
                </p>

                <div class="hero-botoes">
                    <a class="botao laranja" href="profissionais.php">Encontrar profissional</a>
                    <a class="botao escuro" href="cadastro.php">Quero trabalhar</a>
                </div>
            </div>

            <div class="hero-imagem">
                <div class="casa-desenho">🏠</div>
                <div class="selo-disponivel">
                    <span>✓</span>
                    <div>
                        <strong>Profissionais disponíveis</strong>
                        <small>Consulte por região e serviço</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="secao">
        <div class="container">
            <p class="destaque">SERVIÇOS</p>
            <h2>Do que sua casa precisa?</h2>
            <p class="texto-cinza">Escolha uma categoria e encontre profissionais cadastrados.</p>

            <div class="grade-categorias">
                <?php while ($servico = $servicos->fetch_assoc()): ?>
                    <a class="categoria" href="profissionais.php?especialidade=<?php echo urlencode($servico['nome']); ?>">
                        <div class="icone-categoria">🛠</div>
                        <h3><?php echo textoSeguro($servico['nome']); ?></h3>
                        <p>Ver profissionais</p>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <section class="estatisticas">
        <div class="container grade-estatisticas">
            <div>
                <strong>8</strong>
                <span>categorias principais</span>
            </div>
            <div>
                <strong>6</strong>
                <span>profissionais de demonstração</span>
            </div>
            <div>
                <strong>24h</strong>
                <span>acesso à plataforma</span>
            </div>
        </div>
    </section>

    <section class="secao">
        <div class="container">
            <div class="titulo-linha">
                <div>
                    <p class="destaque">PROFISSIONAIS</p>
                    <h2>Bem avaliados na plataforma</h2>
                </div>
                <a href="profissionais.php">Ver todos →</a>
            </div>

            <div class="grade-profissionais">
                <?php while ($profissional = $profissionais->fetch_assoc()): ?>
                    <div class="card-profissional">
                        <div class="perfil-linha">
                            <div class="avatar">
                                <?php echo strtoupper(substr($profissional['nome'], 0, 1)); ?>
                            </div>
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
    </section>

    <section class="secao como-funciona">
        <div class="container">
            <p class="destaque">COMO FUNCIONA</p>
            <h2>Simples do começo ao fim</h2>

            <div class="passos">
                <div class="passo">
                    <span>01</span>
                    <h3>Escolha o serviço</h3>
                    <p>Busque pela categoria ou pelo nome do profissional.</p>
                </div>

                <div class="passo">
                    <span>02</span>
                    <h3>Envie sua solicitação</h3>
                    <p>Explique o problema, informe o endereço e uma data desejada.</p>
                </div>

                <div class="passo">
                    <span>03</span>
                    <h3>Acompanhe e avalie</h3>
                    <p>Converse pelo chat, acompanhe o serviço e deixe sua avaliação.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="secao">
        <div class="container chamada">
            <div>
                <h2>Precisa de ajuda em casa?</h2>
                <p>Crie sua conta e encontre um profissional.</p>
            </div>
            <a class="botao preto" href="cadastro.php">Criar conta</a>
        </div>
    </section>
</main>

<?php include 'includes/rodape.php'; ?>
