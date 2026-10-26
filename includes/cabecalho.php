<div class="barra-contato">
    <div class="container barra-contato-conteudo">
        <span>📍 <?php echo textoSeguro($contato['atendimento']); ?></span>
        <span>
            <a href="tel:<?php echo preg_replace('/\D/', '', $contato['telefone']); ?>">📞 <?php echo textoSeguro($contato['telefone']); ?></a>
            &nbsp;·&nbsp;
            <a href="mailto:<?php echo textoSeguro($contato['email']); ?>">✉ <?php echo textoSeguro($contato['email']); ?></a>
        </span>
    </div>
</div>

<header class="topo">
    <div class="container topo-conteudo">
        <a class="logo" href="index.php">
            <span class="logo-quadrado">M</span>
            MPD <strong>Nexus</strong>
        </a>

        <nav class="menu" id="menu">
            <a href="index.php">Home</a>
            <a href="index.php#servicos">Serviços</a>
            <a href="profissionais.php">Profissionais</a>
            <a href="mapa.php">Localização</a>

            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="meus_servicos.php">Meus serviços</a>
                <a href="logout.php">Sair</a>
            <?php else: ?>
                <a href="login.php">Entrar</a>
                <a href="cadastro.php">Trabalhe conosco</a>
            <?php endif; ?>

            <a class="botao-menu" href="profissionais.php">Orçamento grátis</a>
            <a class="botao-whats" href="<?php echo linkWhatsapp(); ?>" target="_blank" rel="noopener">WhatsApp</a>
        </nav>

        <button class="menu-mobile" id="botao-menu" type="button">☰</button>
    </div>
</header>
