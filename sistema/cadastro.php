<?php
$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $senha = trim($_POST["senha"] ?? "");
    $cpf = preg_replace("/\D/", "", $_POST["cpf"] ?? "");

    if ($nome && $email && $senha && $cpf) {
        try {
            $conn = new PDO("mysql:host=localhost;dbname=saep_preparacao;charset=utf8", "root", "");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("INSERT INTO login (login_nome, login_email, login_senha, login_cpf) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nome, $email, $senha, $cpf]);

            $mensagem = "Cadastro realizado! Você já pode entrar.";
        } catch (PDOException $e) {
            $mensagem = "Não foi possível realizar o cadastro. Verifique os dados e o banco.";
        }
    } else {
        $mensagem = "Preencha todos os campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cadastro — MPDWorks</title>
<link rel="stylesheet" href="landing.css">
</head>
<body>
<div class="container" style="max-width:520px;padding-top:70px;">
  <div class="product-card">
    <h2>Criar conta</h2>
    <?php if ($mensagem): ?>
      <p style="padding:12px;background:#f3f3f3;border-radius:6px;"><?php echo htmlspecialchars($mensagem); ?></p>
    <?php endif; ?>
    <form method="post">
      <p><input name="nome" placeholder="Nome completo" required style="width:100%;padding:12px;"></p>
      <p><input type="email" name="email" placeholder="E-mail" required style="width:100%;padding:12px;"></p>
      <p><input name="cpf" placeholder="CPF" required style="width:100%;padding:12px;"></p>
      <p><input type="password" name="senha" placeholder="Senha" required style="width:100%;padding:12px;"></p>
      <button class="buy-btn" type="submit">Cadastrar</button>
    </form>
    <p style="margin-top:15px;"><a href="login.html">Já tenho uma conta</a> · <a href="index.php">Voltar</a></p>
  </div>
</div>
</body>
</html>
