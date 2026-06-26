<?php
include("conexao.php");

$mensagem = "";

if (!isset($_GET['token']) || empty($_GET['token'])) {
    die("❌ Token inválido.");
}

$token = $_GET['token'];

// VERIFICA TOKEN
$stmt = $conexao->prepare("SELECT id FROM usuarios WHERE reset_token = ? AND reset_expira > NOW()");
$stmt->execute([$token]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("❌ Token inválido ou expirado! <a href='esqueci.php'>Solicitar novo link</a>");
}

// PROCESSA NOVA SENHA
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $senha = $_POST['senha'];

    $regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/";
    if (!preg_match($regex, $senha)) {
        $mensagem = "❌ Senha fraca! Use ao menos 8 caracteres, maiúscula, minúscula, número e símbolo.";
    } else {
        $nova_senha = password_hash($senha, PASSWORD_DEFAULT);

        $upd = $conexao->prepare("UPDATE usuarios SET senha = ?, reset_token = NULL, reset_expira = NULL WHERE reset_token = ?");
        $upd->execute([$nova_senha, $token]);

        echo "✅ Senha alterada com sucesso! Redirecionando...";
        header("refresh:2;url=login.php");
        exit;
    }
}
?>

<html>
<head><title>Redefinir Senha</title></head>
<body>

<h2>Redefinir Senha</h2>

<?php if ($mensagem != "") echo "<p>$mensagem</p>"; ?>

<form method="POST" action="">
    <label>Nova senha:</label><br>
    <input type="password" name="senha" placeholder="Nova senha" required size="30"><br><br>
    <button type="submit">Salvar senha</button>
</form>

</body>
</html>