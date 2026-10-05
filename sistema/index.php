<?php
session_start();
$logado = isset($_SESSION['login_nome']);
$nome_usuario = $logado ? htmlspecialchars($_SESSION['login_nome']) : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MPDWorks — Sua Casa Sempre</title>
  <meta name="description" content="Serviços e produtos para deixar sua casa sempre segura e funcionando." />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="landing.css" />
</head>
<body>

<header class="site-header">
  <div class="container header-inner">
    <a class="brand" href="#inicio" aria-label="Voltar para o início">
      <img src="https://raw.githubusercontent.com/MarceloLTZ/MPD/Front-end/mpdworks-landing-page/assets/logo.png" alt="MPDWorks">
    </a>

    <div class="search-wrap">
      <span class="search-icon" aria-hidden="true">⌕</span>
      <input type="search" id="siteSearch" placeholder="Busque por serviços ou produtos..." aria-label="Buscar" />
    </div>

    <nav class="main-nav" aria-label="Navegação principal">
      <a href="#inicio" class="active">Início</a>
      <a href="#profissionais">Profissionais</a>
      <a href="#servicos">Serviços</a>
    </nav>

    <div class="header-actions">
      <button class="icon-btn" aria-label="Favoritos">♡</button>
      <button class="icon-btn" aria-label="Carrinho">🛒</button>
      <?php if ($logado): ?>
        <a class="login-btn" href="home.php"><?php echo $nome_usuario; ?></a>
      <?php else: ?>
        <a class="login-btn" href="login.php">Entrar</a>
      <?php endif; ?>
    </div>

    <button class="menu-toggle" aria-label="Abrir menu" aria-expanded="false">☰</button>
  </div>
</header>

<main>
  <section id="inicio" class="hero section-pad">
    <div class="container">
      <div class="hero-banner">
        <span>SOLUÇÕES</span> PARA O <span>SEU LAR</span> EM UM SÓ LUGAR
      </div>

      <div class="hero-grid">
        <div class="hero-copy">
          <h1>SUA CASA <span>SEMPRE<br>SEGURA</span> E EM<br>FUNCIONAMENTO!</h1>
          <p>
            Conecte-se com os melhores engenheiros, eletricistas, encanadores e decoradores da sua região.
            E ainda equipe seu lar com produtos de altíssima qualidade em nossa loja exclusiva.
          </p>
          <div class="hero-actions">
            <a class="btn btn-primary" href="#profissionais">Encontrar Profissionais</a>
            <a class="btn btn-secondary" href="#loja">Explorar Loja</a>
          </div>
        </div>

        <div class="hero-media">
          <img src="https://raw.githubusercontent.com/MarceloLTZ/MPD/Front-end/mpdworks-landing-page/assets/hero-profissional.png" alt="Profissional realizando manutenção residencial">
        </div>
      </div>
    </div>
  </section>

  <section id="servicos" class="section-pad services-section">
    <div class="container narrow">
      <span class="eyebrow orange">SERVIÇOS RESIDENCIAIS</span>
      <h2>Nossos Serviços e Especialidades</h2>
      <p class="section-subtitle">Selecione a categoria de profissional ideal para planejar, reparar ou cuidar sua casa.</p>

      <article class="feature-card service-card">
        <img src="https://raw.githubusercontent.com/MarceloLTZ/MPD/Front-end/mpdworks-landing-page/assets/encanador.png" alt="Serviço de encanamento residencial">
        <div class="feature-card-bottom">
          <div>
            <h3>Encanadores</h3>
            <p>Instalações, reparos e manutenção hidráulica residencial.</p>
          </div>
          <span class="arrow">⌄</span>
        </div>
      </article>

      <article id="loja" class="product-card">
        <img src="https://raw.githubusercontent.com/MarceloLTZ/MPD/Front-end/mpdworks-landing-page/assets/fechadura-digital.png" alt="Fechadura digital inteligente biométrica">
        <div class="product-content">
          <small>SafeTech</small>
          <h3>Fechadura Digital Inteligente Biométrica</h3>
          <div class="stars" aria-label="4 de 5 estrelas">★★★★<span>☆</span> <small>(28)</small></div>
          <div class="price-row">
            <strong>R$ 349,00</strong>
            <del>R$ 429,00</del>
          </div>
          <button class="buy-btn" type="button" data-login-required>Comprar agora</button>
        </div>
      </article>
    </div>
  </section>

  <section id="profissionais" class="section-pad professionals-section">
    <div class="container narrow">
      <h2>Profissionais Disponíveis na Região</h2>
      <p class="section-subtitle">Veja os perfis com melhor avaliação dos clientes e contrate diretamente para a sua necessidade.</p>

      <article class="pro-card">
        <div class="pro-top">
          <div class="avatar">ED</div>
          <div class="pro-id">
            <strong>Enzo Diniz</strong>
            <span>Encanador Hidráulico Residencial</span>
          </div>
          <div class="pro-rating">
            <div class="stars">★★★★<span>☆</span> <small>(18)</small></div>
            <strong>Santos - SP</strong>
          </div>
        </div>
        <div class="pro-meta">
          <span>Avaliação</span>
          <span>Localização</span>
        </div>
        <div class="pro-actions">
          <a class="btn-dark" href="login.php">Contratar</a>
          <a class="btn-outline" href="login.php">Perfil Completo</a>
        </div>
      </article>

      <div class="center"><button class="see-more" type="button" data-login-required>Veja mais</button></div>
    </div>
  </section>

  <section class="section-pad how-section" id="como-funciona">
    <div class="container narrow">
      <span class="eyebrow yellow">SIMPLICIDADE</span>
      <h2>Como Funciona a Sua Casa Sempre</h2>
      <p class="section-subtitle">Do reparo rápido à decoração completa, resolvemos tudo para você em poucos passos.</p>

      <div class="steps-grid">
        <article class="step">
          <span class="step-number green">1</span>
          <h3>Escolha o que precisa</h3>
          <p>Selecione se precisa de um especialista técnico credenciado ou se quer navegar pela nossa loja e-commerce.</p>
        </article>
        <article class="step">
          <span class="step-number orange-bg">2</span>
          <h3>Contrate ou Compre</h3>
          <p>Fale diretamente com os prestadores pelo nosso chat seguro ou feche seu pedido de produtos com total garantia de entrega.</p>
        </article>
        <article class="step">
          <span class="step-number gray">3</span>
          <h3>Avalie e Relaxe</h3>
          <p>O serviço foi concluído? O produto chegou? Deixe sua avaliação sincera e ajude a manter nossa comunidade qualificada.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="offer-section">
    <div class="container narrow offer-inner">
      <div>
        <h2>Primeira vez por aqui? Ganhe 15% de desconto em qualquer produto!</h2>
        <p>Cadastre seu e-mail agora e use o cupom oficial na finalização do seu primeiro pedido na nossa e-commerce.</p>
      </div>
      <form class="offer-form" id="offerForm">
        <input type="email" id="offerEmail" placeholder="Seu melhor e-mail..." required />
        <button type="submit">Quero Oferta</button>
      </form>
    </div>
  </section>

  <section class="section-pad testimonials-section">
    <div class="container narrow">
      <span class="eyebrow green-text">FEEDBACKS REAIS</span>
      <h2>O Que Dizem Nossos Clientes</h2>
      <p class="section-subtitle">Milhares de lares transformados e reparados com segurança e transparência.</p>

      <article class="testimonial-card">
        <div class="stars">★★★★<span>☆</span></div>
        <blockquote>“Tive um vazamento urgente de madrugada e encontrei o Enzo pelo site. Em 30 minutos ele resolveu tudo. Uma facilidade incrível.”</blockquote>
        <div class="customer">
          <div class="avatar small">CM</div>
          <div>
            <strong>Cláudia Lima</strong>
            <span>Cliente Emergencial</span>
          </div>
        </div>
      </article>
    </div>
  </section>
</main>

<footer class="footer">
  <div class="container narrow footer-grid">
    <div class="footer-about">
      <h3>SUA CASA SEMPRE</h3>
      <p>A plataforma definitiva para encontrar serviços especializados e produtos de alta qualidade para o seu lar. De pessoas reais para lares reais.</p>
    </div>
    <div>
      <h4>Para o seu Lar</h4>
      <a href="#profissionais">Encontrar Profissional</a>
      <a href="#loja">Explorar E-commerce</a>
      <a href="#como-funciona">Como Funciona</a>
    </div>
    <div>
      <h4>Profissionais</h4>
      <a href="login.php">Seja um Parceiro</a>
      <a href="login.php">Área do Prestador</a>
      <a href="login.php">Central de Ajuda</a>
    </div>
    <div>
      <h4>Contato & Suporte</h4>
      <a href="mailto:suporte@suacasasempre.com">suporte@suacasasempre.com</a>
      <span>0800 123 4567</span>
      <div class="socials" aria-label="Redes sociais"><span>◎</span><span>◉</span><span>▣</span></div>
    </div>
  </div>
  <div class="container narrow footer-bottom">
    <span>© 2026 SUA CASA SEMPRE. Todos os direitos reservados.</span>
    <div><a href="#">Termos de Uso</a><a href="#">Política de Privacidade</a></div>
  </div>
</footer>

<button class="emergency-button" id="emergencyButton" type="button" aria-label="Abrir emergência">🔴 EMERGÊNCIA</button>

<div class="emergency-overlay" id="emergencyOverlay" aria-hidden="true">
  <div class="emergency-modal" role="dialog" aria-modal="true" aria-labelledby="emergencyTitle">
    <button class="emergency-close" id="emergencyClose" type="button" aria-label="Fechar">×</button>
    <h2 id="emergencyTitle">Você precisa de ajuda?</h2>
    <p>Em uma emergência real, escolha o serviço adequado abaixo.</p>
    <div class="emergency-actions">
      <a href="tel:190">🚔 Polícia — 190</a>
      <a href="tel:192">🚑 SAMU — 192</a>
      <a href="tel:193">🚒 Bombeiros — 193</a>
      <a href="login.php">🛠️ Solicitar profissional MPD</a>
    </div>
  </div>
</div>

<div class="toast" id="toast" role="status" aria-live="polite"></div>
<script src="landing.js"></script>
</body>
</html>
