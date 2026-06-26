<?php
include 'conexao.php';

if (isset($_POST['cadastrar'])):

    $nome  = trim($_POST['nome']);
    $senha = $_POST['senha'];
    $email = trim($_POST['email']);

    $regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/";
    if (!preg_match($regex, $senha)) {
        echo "<script>alert('❌ Senha inválida! Precisa ter: mínimo 8 caracteres, letra maiúscula, minúscula, número e símbolo.');</script>";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('❌ E-mail inválido!');</script>";
        exit;
    }

    $check = $conexao->prepare("SELECT email FROM usuarios WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetch()) {
        echo "<script>alert('❌ Este e-mail já está cadastrado!');</script>";
        exit;
    }

    $senha_crip = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $conexao->prepare("INSERT INTO usuarios (nome, senha, email) VALUES (?, ?, ?)");

    if ($stmt->execute([$nome, $senha_crip, $email])) {
        echo "<script>alert('✅ Cadastro realizado! Faça login.'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('❌ Erro ao cadastrar!');</script>";
    }

endif;
?>
<html>
<head>
    <title>CADASTRO</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <div class="card">

        <div class="badge">✦ Crie sua conta</div>

        <h2>Cadastro</h2>

        <form method="post" action="" autocomplete="off">

            <div>
                <label>Nome</label>
                <input name="nome" type="text" required placeholder="Seu nome completo">
            </div>

            <div>
                <label>Email</label>
                <input name="email" type="email" required placeholder="seu@email.com">
            </div>

            <div>
                <label>Senha</label>
                <input name="senha" type="password" required placeholder="Min. 8 caracteres">
            </div>

            <button type="submit" name="cadastrar" class="btn">Criar conta →</button>

        </form>

        <a href="login.php" class="link">Já tenho uma conta</a>

    </div>
</div>

</body>
</html>