<?php
// Envio de e-mail por SMTP usando só PHP (sem bibliotecas extras).

function smtpLer($fp) {
    $resposta = '';
    while (($linha = fgets($fp, 515)) !== false) {
        $resposta .= $linha;
        if (strlen($linha) < 4 || $linha[3] === ' ') {
            break;
        }
    }
    return $resposta;
}

// Envia um comando (ou só lê, se $comando for null) e confere o código de resposta
function smtpComando($fp, $comando, $codigoEsperado) {
    if ($comando !== null) {
        fwrite($fp, $comando . "\r\n");
    }
    return substr(smtpLer($fp), 0, 3) === $codigoEsperado;
}

function enviarEmail($para, $assunto, $html) {
    global $smtp;

    if (empty($smtp['host'])) {
        return false; // modo teste: não envia
    }

    $usuario = $smtp['usuario'];
    $remetente = $smtp['remetente'] ?: $usuario;
    $verificar = !empty($smtp['verificar_certificado']);

    $contexto = stream_context_create(['ssl' => [
        'verify_peer' => $verificar,
        'verify_peer_name' => $verificar,
        'allow_self_signed' => !$verificar,
    ]]);

    $prefixo = $smtp['seguranca'] === 'ssl' ? 'ssl://' : 'tcp://';
    $fp = @stream_socket_client($prefixo . $smtp['host'] . ':' . $smtp['porta'], $erroNum, $erroTexto, 15, STREAM_CLIENT_CONNECT, $contexto);

    if (!$fp) {
        return false;
    }

    stream_set_timeout($fp, 15);
    $ok = smtpComando($fp, null, '220') && smtpComando($fp, 'EHLO localhost', '250');

    if ($ok && $smtp['seguranca'] === 'tls') {
        $ok = smtpComando($fp, 'STARTTLS', '220')
            && stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)
            && smtpComando($fp, 'EHLO localhost', '250');
    }

    if ($ok && $usuario !== '') {
        $ok = smtpComando($fp, 'AUTH LOGIN', '334')
            && smtpComando($fp, base64_encode($usuario), '334')
            && smtpComando($fp, base64_encode($smtp['senha']), '235');
    }

    if ($ok) {
        $cabecalhos = "From: =?UTF-8?B?" . base64_encode($smtp['nome']) . "?= <" . $remetente . ">\r\n"
            . "To: <" . $para . ">\r\n"
            . "Subject: =?UTF-8?B?" . base64_encode($assunto) . "?=\r\n"
            . "MIME-Version: 1.0\r\n"
            . "Content-Type: text/html; charset=UTF-8\r\n"
            . "Content-Transfer-Encoding: base64\r\n";

        $ok = smtpComando($fp, 'MAIL FROM:<' . $remetente . '>', '250')
            && smtpComando($fp, 'RCPT TO:<' . $para . '>', '250')
            && smtpComando($fp, 'DATA', '354')
            && smtpComando($fp, $cabecalhos . "\r\n" . chunk_split(base64_encode($html)) . "\r\n.", '250');
    }

    @fwrite($fp, "QUIT\r\n");
    fclose($fp);

    return $ok;
}
?>
