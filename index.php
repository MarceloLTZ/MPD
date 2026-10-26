<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$titulo = 'MPD Nexus - Início';

$servicos = $conexao->query("SELECT * FROM servicos WHERE ativo = 1 ORDER BY nome LIMIT 8");

// Números reais do banco
$totalServicos = $conexao->query("SELECT COUNT(*) AS n FROM servicos WHERE ativo = 1")->fetch_assoc()['n'];
$totalProfissionais = $conexao->query("SELECT COUNT(*) AS n FROM profissionais WHERE ativo = 1")->fetch_assoc()['n'];
$totalAvaliacoes = $conexao->query("SELECT COUNT(*) AS n FROM avaliacoes")->fetch_assoc()['n'];
$profissionais = $conexao->query("SELECT * FROM profissionais WHERE ativo = 1 ORDER BY nota DESC LIMIT 4");

include 'includes/inicio_html.php';
include 'includes/cabecalho.php';
?>

<main>
    <section class="hero" style="background-image: linear-gradient(90deg, rgba(17,17,20,.92) 35%, rgba(17,17,20,.55)), url('<?php echo textoSeguro($imagens['hero']); ?>');">
        <div class="container hero-conteudo">
            <div class="hero-texto">
                <p class="destaque">MANUTENÇÃO RESIDENCIAL</p>
                <h1>Pequenos problemas em casa? Encontre quem resolve hoje.</h1>
                <p class="hero-descricao">
                    Eletricista, encanador, pintor e muito mais em um só lugar. Escolha o profissional, envie sua solicitação e acompanhe tudo pelo site.
                </p>

                <form class="hero-busca" action="profissionais.php" method="get">
                    <input type="text" name="q" placeholder="Qual serviço você precisa? Ex.: eletricista">
                    <button class="botao amarelo" type="submit">Buscar</button>
                </form>

                <div class="hero-botoes">
                    <a class="botao whats" href="<?php echo linkWhatsapp(); ?>" target="_blank" rel="noopener">Falar no WhatsApp agora</a>
                    <a class="botao contorno" href="cadastro.php">Quero trabalhar</a>
                </div>
            </div>
        </div>
    </section>

    <section class="destaques">
        <div class="container grade-destaques">
            <div><strong>✔ Profissionais avaliados</strong><span>Notas e comentários de clientes</span></div>
            <div><strong>✔ Atendimento rápido</strong><span>Chat direto com o profissional</span></div>
            <div><strong>✔ Preço combinado antes</strong><span>Valor da visita visível no perfil</span></div>
        </div>
    </section>

    <section class="secao" id="servicos">
        <div class="container">
            <p class="destaque">SERVIÇOS</p>
            <h2>Do que sua casa precisa?</h2>
            <p class="texto-cinza">Escolha uma categoria e encontre profissionais cadastrados.</p>

            <div class="grade-categorias">
                <?php while ($servico = $servicos->fetch_assoc()): ?>
                    <a class="categoria" href="profissionais.php?especialidade=<?php echo urlencode($servico['nome']); ?>">
                        <?php if (!empty($servico['imagem_url'])): ?>
                            <img class="imagem-categoria" src="<?php echo textoSeguro($servico['imagem_url']); ?>" alt="<?php echo textoSeguro($servico['nome']); ?>" loading="lazy" onerror="this.style.display='none'">
                        <?php endif; ?>
                        <div class="categoria-texto">
                            <h3><?php echo textoSeguro($servico['nome']); ?></h3>
                            <p>Ver profissionais →</p>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <section class="secao cinza como-funciona">
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
                            <img class="foto-card" src="<?php echo textoSeguro(fotoProfissional($profissional)); ?>" alt="<?php echo textoSeguro($profissional['nome']); ?>" data-fallback="<?php echo textoSeguro(avatarGerado($profissional['nome'])); ?>" onerror="this.onerror=null;this.src=this.dataset.fallback">
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

    <section class="estatisticas">
        <div class="container grade-estatisticas">
            <div>
                <strong><?php echo intval($totalServicos); ?></strong>
                <span>categorias de serviço</span>
            </div>
            <div>
                <strong><?php echo intval($totalProfissionais); ?></strong>
                <span>profissionais cadastrados</span>
            </div>
            <div>
                <strong><?php echo intval($totalAvaliacoes); ?></strong>
                <span>avaliações de clientes</span>
            </div>
            <div>
                <strong>24h</strong>
                <span>acesso à plataforma</span>
            </div>
        </div>
    </section>

    <section class="secao">
        <div class="container chamada">
            <div>
                <h2>Pequenos e grandes problemas, a gente tem a solução!</h2>
                <p>Crie sua conta e encontre um profissional agora mesmo.</p>
            </div>
            <div class="chamada-botoes">
                <a class="botao amarelo" href="cadastro.php">Criar conta</a>
                <a class="botao whats" href="<?php echo linkWhatsapp(); ?>" target="_blank" rel="noopener">WhatsApp</a>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/rodape.php'; ?>
