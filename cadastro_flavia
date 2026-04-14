<?php

function validarCPF($cpf) {
 
    // Extrai somente os números
    $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
     
    // Verifica se foi informado todos os digitos corretamente
    if (strlen($cpf) != 11) {
        return false;
    }

    // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    // Faz o calculo para validar o CPF
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    return true;

}
// $conexao = mysqli_connect("localhost","root","","telefone_email");
// mysqli_select_db($conexao, "telefone_email");
include "conexao.php";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
</head>
<body>
    <form action="" method="post">
        <label for="">Email/Telefone/cpf</label>
        <input type="text" name="email_telefone_cpf" autocomplete='off' required>
        <label for="">Senha</label>
        <input type="password" name="senha" required>
        <button type="submit" name="enviar">Enviar</button>

    </form>
</body>
</html>
<?php
if(isset($_POST["enviar"])){
    $email_telefone_cpf = $_POST["email_telefone_cpf"];
    $senha = $_POST["senha"];
    if (preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/', $senha)) {
        $senha_crip = password_hash($senha, PASSWORD_DEFAULT);  
    }
    if (filter_var($email_telefone_cpf, FILTER_VALIDATE_EMAIL) ){
        mysqli_query($conexao, "INSERT INTO cadastro(emailcelularcpf,senha) VALUES('$email_telefone_cpf','$senha_crip')");
        
        }
    if(preg_match('/^[0-9]{10,11}$/', $email_telefone_cpf) and !validarCPF($_POST["email_telefone_cpf"])){
        mysqli_query($conexao, "INSERT INTO cadastro(emailcelularcpf,senha) VALUES('$email_telefone_cpf','$senha_crip')");
    }
    if(validarCPF($_POST["email_telefone_cpf"])){
        $email_telefone_cpf_crip = password_hash($email_telefone_cpf, PASSWORD_DEFAULT);
        mysqli_query($conexao, "INSERT INTO cadastro(emailcelularcpf,senha) VALUES('$email_telefone_cpf_crip','$senha_crip')");
    }
}
