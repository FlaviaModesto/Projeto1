<?php
session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit;
}

include 'conexao.php';

$nome_usuario = $_SESSION['usuario'];
$inicial = strtoupper(mb_substr($nome_usuario, 0, 1));
$mensagem = "";
$tipo = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cadastrar_produto'])) {

    $nome       = trim($_POST['nome_produto']);
    $descricao  = trim($_POST['descricao']);
    $preco      = $_POST['preco'];
    $quantidade = (int) $_POST['quantidade'];

    if (empty($nome) || empty($preco) || $quantidade < 0) {
        $mensagem = "❌ Preencha todos os campos obrigatórios.";
        $tipo = "erro";
    } else {
        $stmt = $conexao->prepare("INSERT INTO produtos (nome, descricao, preco, quantidade) VALUES (?, ?, ?, ?)");

        if ($stmt->execute([$nome, $descricao, $preco, $quantidade])) {
            $mensagem = "✅ Produto cadastrado com sucesso!";
            $tipo = "sucesso";
        } else {
            $mensagem = "❌ Erro ao cadastrar produto.";
            $tipo = "erro";
        }
    }
}
?>

<html>
<head>
    <title>Cadastrar Produto</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-body">

<div class="dashboard-layout">

    <aside class="sidebar">

        <div class="sidebar-brand">
            <div class="sidebar-logo">S</div>
            <span>Sistema</span>
        </div>

        <div class="sidebar-user">
            <div class="sidebar-avatar"><?php echo $inicial; ?></div>
            <div class="sidebar-user-info">
                <span class="sidebar-user-name"><?php echo htmlspecialchars($nome_usuario); ?></span>
                <span class="sidebar-user-role">Usuário</span>
            </div>
        </div>

        <nav class="sidebar-nav">

            <div class="nav-label">Menu</div>

            <a href="home.php" class="nav-item">
                <span class="nav-icon">⊞</span>
                Início
            </a>

            <a href="cadastrar_produto.php" class="nav-item active">
                <span class="nav-icon">＋</span>
                Cadastrar Produto
            </a>

            <a href="pesquisar_produto.php" class="nav-item">
                <span class="nav-icon">⊙</span>
                Pesquisar Produtos
            </a>

        </nav>

        <a href="logout.php" class="nav-item nav-logout">
            <span class="nav-icon">⏻</span>
            Sair da conta
        </a>

    </aside>

    <main class="dashboard-main">

        <div class="dash-header">
            <div>
                <div class="badge">✦ Estoque</div>
                <h1>Cadastrar <span>Produto</span></h1>
                <p class="subtitle">Adicione um novo item ao seu estoque.</p>
            </div>
        </div>

        <div class="form-card">

            <?php if ($mensagem != ""): ?>
                <p class="msg msg-<?php echo $tipo; ?>"><?php echo $mensagem; ?></p>
            <?php endif; ?>

            <form method="POST" action="" autocomplete="off">

                <div>
                    <label>Nome do produto *</label>
                    <input type="text" name="nome_produto" required placeholder="Ex: Camiseta Básica">
                </div>

                <div>
                    <label>Descrição</label>
                    <textarea name="descricao" placeholder="Descreva o produto..." rows="3"></textarea>
                </div>

                <div class="form-row">
                    <div>
                        <label>Preço (R$) *</label>
                        <input type="number" name="preco" required placeholder="0,00" step="0.01" min="0">
                    </div>
                    <div>
                        <label>Quantidade *</label>
                        <input type="number" name="quantidade" required placeholder="0" min="0">
                    </div>
                </div>

                <button type="submit" name="cadastrar_produto" class="btn">Salvar produto →</button>

            </form>

        </div>

    </main>

</div>

</body>
</html>