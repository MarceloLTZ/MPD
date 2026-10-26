<footer class="rodape">
    <div class="container rodape-conteudo">
        <div>
            <h3>MPD <span>Nexus</span></h3>
            <p>Conectamos você a profissionais de manutenção residencial: elétrica, hidráulica, pintura e muito mais.</p>
        </div>

        <div>
            <h4>Navegação</h4>
            <a href="index.php">Home</a>
            <a href="profissionais.php">Profissionais</a>
            <a href="mapa.php">Localização</a>
        </div>

        <div>
            <h4>Conta</h4>
            <a href="login.php">Entrar</a>
            <a href="cadastro.php">Cadastrar</a>
        </div>

        <div>
            <h4>Contato</h4>
            <a href="tel:<?php echo preg_replace('/\D/', '', $contato['telefone']); ?>"><?php echo textoSeguro($contato['telefone']); ?></a>
            <a href="<?php echo linkWhatsapp(); ?>" target="_blank" rel="noopener">WhatsApp</a>
            <a href="mailto:<?php echo textoSeguro($contato['email']); ?>"><?php echo textoSeguro($contato['email']); ?></a>
        </div>
    </div>

    <p class="copyright">© 2026 MPD Nexus - Projeto acadêmico.</p>
</footer>

<script src="js/script.js"></script>
<?php echo isset($extraScript) ? $extraScript : ''; ?>
</body>
</html>
