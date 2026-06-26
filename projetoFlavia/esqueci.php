<?php
include("conexao.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "vendor/autoload.php";

$mensagem = "";
$tipo = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "❌ Email inválido!";
        $tipo = "erro";
    } else {

        $stmt = $conexao->prepare("SELECT email FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {

            $novaSenha = substr(bin2hex(random_bytes(8)), 0, 10);
            $senhaCrip = password_hash($novaSenha, PASSWORD_DEFAULT);

            $upd = $conexao->prepare("UPDATE usuarios SET senha = ? WHERE email = ?");
            $upd->execute([$senhaCrip, $email]);

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'lamine.yamalk10@gmail.com';
                $mail->Password   = 'yaop yvpo wdwq lzhf';
                $mail->SMTPSecure = 'ssl';
                $mail->Port       = 465;

                $mail->setFrom('lamine.yamalk10@gmail.com', 'Sistema');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Nova senha';
                $mail->Body    = "Sua nova senha é: <b>$novaSenha</b><br>Recomendamos que você a altere após o login.";

                $mail->send();
                $mensagem = "✅ Nova senha enviada para seu e-mail!";
                $tipo = "sucesso";

            } catch (Exception $e) {
                $mensagem = "❌ Erro ao enviar email: {$mail->ErrorInfo}";
                $tipo = "erro";
            }

        } else {
            $mensagem = "❌ Email não encontrado!";
            $tipo = "erro";
        }
    }
}
?>

<html>
<head>
    <title>Esqueci a Senha</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <div class="card">

        <div class="badge">✦ Recuperar acesso</div>

        <h2>Esqueci a senha</h2>

        <p class="subtitle">Digite seu e-mail e enviaremos uma nova senha para você.</p>

        <form method="POST" action="">

            <div>
                <label>Email</label>
                <input type="email" name="email" required placeholder="seu@email.com">
            </div>

            <button type="submit" class="btn">Enviar nova senha →</button>

        </form>

        <?php if($mensagem != ""): ?>
            <p><?php echo $mensagem; ?></p>
        <?php endif; ?>

        <a href="login.php" class="link">← Voltar ao login</a>

    </div>
</div>

</body>
</html>