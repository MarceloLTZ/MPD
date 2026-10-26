<?php
// ============================================================
// CONFIGURAÇÃO DO SITE  (é o único arquivo que você precisa editar)
// ============================================================

// ---- BANCO DE DADOS NA NUVEM (MySQL) ----
// Cole aqui os dados que o provedor (ex.: Aiven, TiDB Cloud, Clever Cloud) te deu.
// Enquanto estiver "localhost", o site usa o MySQL do seu computador (XAMPP).
// Dica: para NÃO deixar a senha no código (e não subir ela para o GitHub), defina as variáveis
// de ambiente DB_HOST, DB_PORTA, DB_USUARIO, DB_SENHA, DB_BANCO e DB_SSL (=1).
// No Codespaces: GitHub > Settings > Codespaces > Secrets. Elas têm prioridade sobre os valores abaixo.
$config = [
    'host'    => getenv('DB_HOST')    ?: 'localhost',   // ex.: mysql-xxxx.aivencloud.com
    'porta'   => getenv('DB_PORTA')   ?: 3306,          // ex.: 12345 (o provedor informa)
    'usuario' => getenv('DB_USUARIO') ?: 'root',
    'senha'   => getenv('DB_SENHA')   ?: '1234',
    'banco'   => getenv('DB_BANCO')   ?: 'mpd_nexus',   // ex.: defaultdb
    'ssl'     => getenv('DB_SSL')     ? true : false,   // true quando usar banco na nuvem
];

// ---- CONTATOS QUE APARECEM NO SITE ----
$contato = [
    'telefone'      => '(11) 99999-9999',
    'whatsapp'      => '5511999999999',   // só números, com 55 + DDD
    'email'         => 'contato@mpdnexus.com.br',
    'atendimento'   => 'Grande ABC - Mauá, Santo André, São Bernardo e região',
];

// ---- IMAGENS POR LINK (nada fica salvo no computador) ----
// Para trocar uma foto, é só colar outro link aqui.
$imagens = [
    'hero'    => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&w=1600&q=70',
    'servico' => 'https://images.unsplash.com/photo-1621905252507-b35492cc74b4?auto=format&fit=crop&w=1000&q=70',
];

// ---- E-MAIL (confirmação de cadastro e "esqueci minha senha") ----
// Deixe 'host' VAZIO para o MODO TESTE: o site não envia e-mail e mostra o link na própria tela
// (ótimo para apresentar o TCC sem configurar nada).
// Para enviar e-mail de verdade, use um SMTP. Exemplo com Gmail:
//   host = smtp.gmail.com | porta = 465 | seguranca = ssl | usuario = seu@gmail.com
//   senha = "senha de app" do Google (Conta Google > Segurança > Senhas de app)
// Dica: use variáveis de ambiente (SMTP_HOST, SMTP_PORTA, SMTP_SEGURANCA, SMTP_USUARIO,
// SMTP_SENHA, SMTP_REMETENTE, SITE_URL) para não deixar a senha no código.
$smtp = [
    'host'      => getenv('SMTP_HOST')      ?: '',
    'porta'     => getenv('SMTP_PORTA')     ?: 465,
    'seguranca' => getenv('SMTP_SEGURANCA') ?: 'ssl',   // 'ssl' (porta 465) ou 'tls' (porta 587)
    'usuario'   => getenv('SMTP_USUARIO')   ?: '',
    'senha'     => getenv('SMTP_SENHA')     ?: '',
    'remetente' => getenv('SMTP_REMETENTE') ?: '',      // vazio = usa o mesmo e-mail do usuário
    'nome'      => 'MPD Nexus',
    'verificar_certificado' => false,                   // true = mais seguro (precisa de certificados no PHP)
    'url_site'  => getenv('SITE_URL')       ?: '',      // ex.: https://meusite.com/MPD_Nexus (vazio = detecta sozinho)
    'exigir_confirmacao' => true,                       // false = entra direto, sem confirmar e-mail
];
