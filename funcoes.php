<?php
require_once __DIR__ . '/email.php';

function obrigarLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit;
    }
}

function textoSeguro($texto) {
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

// Imagem com as iniciais do nome (usada quando a foto não existe ou não carrega)
function avatarGerado($nome) {
    return 'https://ui-avatars.com/api/?size=200&background=f0701c&color=111114&bold=true&name=' . urlencode($nome);
}

// Foto do profissional: usa o link salvo no banco; se não tiver, gera uma pelo nome
function fotoProfissional($profissional) {
    if (!empty($profissional['foto_url'])) {
        return $profissional['foto_url'];
    }
    return avatarGerado($profissional['nome']);
}

// Link do WhatsApp com mensagem pronta
function linkWhatsapp($mensagem = 'Olá! Gostaria de um orçamento.') {
    global $contato;
    return 'https://wa.me/' . $contato['whatsapp'] . '?text=' . urlencode($mensagem);
}

// ---------- Confirmação de e-mail e recuperação de senha ----------

// Endereço do site para montar os links enviados por e-mail
function urlBase() {
    global $smtp;

    if (!empty($smtp['url_site'])) {
        return rtrim($smtp['url_site'], '/');
    }

    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    $pasta = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

    return ($https ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $pasta;
}

// Modo teste = e-mail não configurado (o link aparece na tela em vez de ir por e-mail)
function emailEmModoTeste() {
    global $smtp;
    return empty($smtp['host']);
}

// Cria um token aleatório. O e-mail leva o token; o banco guarda só o "hash" dele.
function gerarToken() {
    $token = bin2hex(random_bytes(32));
    return [$token, hash('sha256', $token)];
}

function enviarLink($para, $nome, $assunto, $texto, $link, $textoBotao) {
    $html = '<div style="font-family:Arial,sans-serif;max-width:480px;margin:auto;color:#111114">'
        . '<h2>MPD Nexus</h2>'
        . '<p>Olá, ' . textoSeguro($nome) . '!</p>'
        . '<p>' . $texto . '</p>'
        . '<p><a href="' . textoSeguro($link) . '" style="background:#f0701c;color:#111114;padding:12px 22px;border-radius:6px;text-decoration:none;font-weight:bold">' . $textoBotao . '</a></p>'
        . '<p style="color:#666;font-size:13px">Se o botão não funcionar, copie este endereço no navegador:<br>' . textoSeguro($link) . '</p>'
        . '</div>';

    return enviarEmail($para, $assunto, $html);
}

function enviarConfirmacao($conexao, $id, $nome, $para) {
    list($token, $hash) = gerarToken();

    $stmt = $conexao->prepare("UPDATE usuarios SET token_confirmacao = ? WHERE id = ?");
    $stmt->bind_param('si', $hash, $id);
    $stmt->execute();

    $link = urlBase() . '/confirmar_email.php?token=' . $token;
    $enviado = enviarLink($para, $nome, 'Confirme seu e-mail - MPD Nexus', 'Clique no botão abaixo para confirmar seu e-mail e ativar sua conta.', $link, 'Confirmar e-mail');

    return ['enviado' => $enviado, 'link' => $link];
}

function enviarRecuperacao($conexao, $id, $nome, $para) {
    list($token, $hash) = gerarToken();

    $stmt = $conexao->prepare("UPDATE usuarios SET token_recuperacao = ?, token_expira = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = ?");
    $stmt->bind_param('si', $hash, $id);
    $stmt->execute();

    $link = urlBase() . '/redefinir_senha.php?token=' . $token;
    $enviado = enviarLink($para, $nome, 'Redefinir senha - MPD Nexus', 'Recebemos um pedido para redefinir sua senha. O link vale por 1 hora. Se não foi você, ignore este e-mail.', $link, 'Criar nova senha');

    return ['enviado' => $enviado, 'link' => $link];
}

// Aviso mostrado só no modo teste, com o link que iria por e-mail
function avisoModoTeste($envio) {
    if (!emailEmModoTeste() || !$envio) {
        return '';
    }
    return '<div class="mensagem aviso"><strong>Modo teste:</strong> o envio de e-mail não está configurado, '
        . 'então o link aparece aqui:<br><a href="' . textoSeguro($envio['link']) . '">' . textoSeguro($envio['link']) . '</a></div>';
}
?>
