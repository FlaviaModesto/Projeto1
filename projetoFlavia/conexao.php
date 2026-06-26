<?php
$host  = "localhost";
$banco = "projeto";
$user  = "root";
$pass  = "";

try {
    $conexao = new PDO("mysql:host=$host;dbname=$banco;charset=utf8mb4", $user, $pass);
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>