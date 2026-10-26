<?php
function obrigarLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit;
    }
}

function textoSeguro($texto) {
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}
?>
