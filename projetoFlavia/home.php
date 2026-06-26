<?php
session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit;
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

include 'conexao.php';

$nome    = $_SESSION['usuario'];
$inicial = strtoupper(mb_substr($nome, 0, 1));

$total_produtos = 0;
$total_itens    = 0;
$res = $conexao->query("SELECT COUNT(*) AS total, COALESCE(SUM(quantidade),0) AS itens FROM produtos");
if ($res) {
    $row = $res->fetch(PDO::FETCH_ASSOC);
    $total_produtos = $row['total'];
    $total_itens    = $row['itens'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Painel — Sistema</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script>
        window.history.forward();
        function noBack(){ window.history.forward(); }
        setTimeout(noBack, 0);
        window.onpageshow = function(e){ if(e.persisted) noBack(); };
    </script>
</head>
<body onload="noBack();" class="dashboard-body">

<div class="dashboard-layout">

    <aside class="sidebar">

        <div class="sidebar-brand">
            <div class="sidebar-logo">⚔</div>
            <span>Sistema</span>
        </div>

        <div class="sidebar-user">
            <div class="sidebar-avatar"><?php echo $inicial; ?></div>
            <div class="sidebar-user-info">
                <span class="sidebar-user-name"><?php echo htmlspecialchars($nome); ?></span>
                <span class="sidebar-user-role">Usuário</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-label">Menu</div>

            <a href="home.php" class="nav-item active">
                <span class="nav-icon">⊞</span>
                Início
            </a>

            <a href="cadastrar_produto.php" class="nav-item">
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
                <div class="badge">✦ Área restrita</div>
                <h1>Olá, <span><?php echo htmlspecialchars($nome); ?></span> 👋</h1>
                <p class="subtitle">Bem-vindo ao painel de controle do sistema.</p>
            </div>
        </div>

        <div class="dash-cards">

            <a href="cadastrar_produto.php" class="dash-card">
                <div class="dash-card-icon">＋</div>
                <div class="dash-card-content">
                    <div class="dash-card-title">Cadastrar Produto</div>
                    <div class="dash-card-desc">Adicione novos produtos ao sistema</div>
                </div>
                <span class="dash-card-arrow">→</span>
            </a>

            <a href="pesquisar_produto.php" class="dash-card">
                <div class="dash-card-icon">⊙</div>
                <div class="dash-card-content">
                    <div class="dash-card-title">Pesquisar Produtos</div>
                    <div class="dash-card-desc">Consulte e gerencie o estoque</div>
                </div>
                <span class="dash-card-arrow">→</span>
            </a>

            <a href="pesquisar_produto.php" class="dash-card">
                <div class="dash-card-icon">✎</div>
                <div class="dash-card-content">
                    <div class="dash-card-title">Editar Produto</div>
                    <div class="dash-card-desc">Atualize informações do estoque</div>
                </div>
                <span class="dash-card-arrow">→</span>
            </a>

        </div>

        <div class="stats dash-stats">
            <div class="stat-box">
                <div class="stat-num"><?php echo $total_produtos; ?></div>
                <div class="stat-label">Produtos</div>
            </div>
            <div class="stat-box">
                <div class="stat-num"><?php echo $total_itens; ?></div>
                <div class="stat-label">Itens em estoque</div>
            </div>
            <div class="stat-box">
                <div class="stat-num">✓</div>
                <div class="stat-label">Sistema ativo</div>
            </div>
        </div>

    </main>

</div>

</body>
</html>