<header class="topo">
    <div class="container topo-conteudo">
        <a class="logo" href="index.php">
            <span class="logo-quadrado">M</span>
            MPD <strong>Nexus</strong>
        </a>

        <form class="busca-topo" action="profissionais.php" method="get">
            <input type="text" name="q" placeholder="Buscar profissional ou serviço">
            <button type="submit">Buscar</button>
        </form>

        <nav class="menu" id="menu">
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <div class="usuario-logado">
                    <img class="foto-perfil" src="img/perfil-padrao.svg" alt="Foto de perfil do usuário">
                    <span class="bem-vindo">Bem-vindo, <?php echo textoSeguro($_SESSION['usuario_nome']); ?>!</span>
                </div>
            <?php endif; ?>

            <a href="index.php">Início</a>
            <a href="profissionais.php">Profissionais</a>
            <a href="mapa.php">Localização</a>

            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="meus_servicos.php">Meus serviços</a>
                <a href="logout.php">Sair</a>
            <?php else: ?>
                <a href="login.php">Entrar</a>
                <a class="botao-menu" href="cadastro.php">Cadastrar</a>
            <?php endif; ?>
        </nav>

        <button class="menu-mobile" id="botao-menu" type="button">☰</button>
    </div>
</header>
