<?php
include 'conexao.php';
session_start();

if (isset($_POST['logar'])):

    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    $stmt = $conexao->prepare("SELECT nome, senha FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        if (password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario'] = $usuario['nome'];
            header("Location: home.php");
            exit;
        } else {
            echo "<script>alert('❌ Senha incorreta!');</script>";
        }
    } else {
        echo "<script>alert('❌ Email não encontrado!');</script>";
    }

endif;
?>
<html>
<head>
    <title>LOGIN</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <div class="card">

        <div class="badge">✦ Acesse sua conta</div>

        <h2>Bem-vindo de volta</h2>

        <form method="post" action="" autocomplete="off">

            <div>
                <label>Email</label>
                <input name="email" type="email" required placeholder="seu@email.com">
            </div>

            <div>
                <label>Senha</label>
                <input name="senha" type="password" required placeholder="••••••••">
            </div>

            <button type="submit" name="logar" class="btn">Entrar →</button>

        </form>

        <a href="esqueci.php" class="link">Esqueci minha senha</a>
        <a href="cadastro.php" class="link">Não tenho conta ainda</a>

    </div>
</div>

</body>
</html>