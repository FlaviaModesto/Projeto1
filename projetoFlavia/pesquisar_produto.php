<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

include 'conexao.php';

$nome_usuario = $_SESSION['usuario'];
$inicial = strtoupper(mb_substr($nome_usuario, 0, 1));

// ── DELETAR ────────────────────────────────────────────────
if (isset($_GET['deletar'])) {
    $id   = (int) $_GET['deletar'];
    $stmt = $conexao->prepare("DELETE FROM produtos WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: pesquisar_produto.php?deleted=1");
    exit;
}

// ── BUSCA ──────────────────────────────────────────────────
$busca    = "";
$produtos = [];
$erro_sql = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" || isset($_GET['q'])) {
    $busca = isset($_GET['q']) ? trim($_GET['q']) : trim($_POST['busca'] ?? '');
    $termo = "%" . $busca . "%";

    $stmt = $conexao->prepare("SELECT id, nome, descricao, preco, quantidade FROM produtos WHERE nome LIKE ? OR descricao LIKE ? ORDER BY nome ASC");

    if (!$stmt) {
        $erro_sql = "Erro ao preparar busca.";
    } else {
        $stmt->execute([$termo, $termo]);
        $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} else {
    $stmt = $conexao->query("SELECT id, nome, descricao, preco, quantidade FROM produtos ORDER BY nome ASC");
    if (!$stmt) {
        $erro_sql = "Erro ao carregar produtos.";
    } else {
        $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Pesquisar Produtos</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <style>
        .btn-edit {
            background: rgba(74,31,168,0.2);
            color: var(--violet-light);
            border: 1px solid rgba(124,58,237,0.3);
        }
        .btn-edit:hover {
            background: rgba(124,58,237,0.35);
            color: #fff;
            border-color: var(--violet-bright);
        }
        .td-actions { display: flex; gap: 6px; }
    </style>
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
            <a href="home.php" class="nav-item"><span class="nav-icon">⊞</span> Início</a>
            <a href="cadastrar_produto.php" class="nav-item"><span class="nav-icon">＋</span> Cadastrar Produto</a>
            <a href="pesquisar_produto.php" class="nav-item active"><span class="nav-icon">⊙</span> Pesquisar Produtos</a>
        </nav>
        <a href="logout.php" class="nav-item nav-logout"><span class="nav-icon">⏻</span> Sair da conta</a>
    </aside>

    <main class="dashboard-main">

        <div class="dash-header">
            <div>
                <div class="badge">✦ Estoque</div>
                <h1>Pesquisar <span>Produtos</span></h1>
                <p class="subtitle">Consulte e gerencie os produtos cadastrados.</p>
            </div>
        </div>

        <?php if (isset($_GET['deleted'])): ?>
            <p class="msg msg-sucesso">✅ Produto removido com sucesso.</p>
        <?php endif; ?>

        <?php if (isset($_GET['edited'])): ?>
            <p class="msg msg-sucesso">✅ Produto atualizado com sucesso.</p>
        <?php endif; ?>

        <?php if ($erro_sql): ?>
            <div style="background:#3b0000;border:1px solid #ef4444;border-radius:8px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:#fca5a5;font-family:monospace;font-size:.88rem;">
                <strong>⚠ Erro de banco de dados:</strong><br>
                <?php echo htmlspecialchars($erro_sql); ?><br><br>
                <strong>Solução:</strong> A tabela <code>produtos</code> provavelmente não existe. Rode este SQL no phpMyAdmin:<br><br>
                <code style="display:block;background:#1a0000;padding:.75rem;border-radius:6px;white-space:pre-wrap;">CREATE TABLE IF NOT EXISTS produtos (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  nome        VARCHAR(150) NOT NULL,
  descricao   TEXT,
  preco       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  quantidade  INT NOT NULL DEFAULT 0,
  criado_em   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);</code>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST" action="" autocomplete="off" style="flex-direction:row; gap:12px;">
                <div style="flex:1;">
                    <input type="text" name="busca" placeholder="Buscar por nome ou descrição..." value="<?php echo htmlspecialchars($busca); ?>">
                </div>
                <button type="submit" class="btn" style="width:auto; margin-top:0;">Buscar →</button>
            </form>
        </div>

        <div class="table-wrap">
            <?php if (empty($produtos) && !$erro_sql): ?>
                <p class="empty-state">Nenhum produto encontrado.</p>
            <?php elseif (!empty($produtos)): ?>
                <table class="produto-table">
                    <thead>
                        <tr>
                            <th>#</th><th>Nome</th><th>Descrição</th><th>Preço</th><th>Qtd</th><th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtos as $p): ?>
                        <tr>
                            <td><?php echo $p['id']; ?></td>
                            <td><?php echo htmlspecialchars($p['nome']); ?></td>
                            <td><?php echo htmlspecialchars($p['descricao'] ?? '—'); ?></td>
                            <td>R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?></td>
                            <td><?php echo $p['quantidade']; ?></td>
                            <td>
                                <div class="td-actions">
                                    <a href="editar_produto.php?id=<?php echo $p['id']; ?>" class="btn-table btn-edit" title="Editar">✎</a>
                                    <a href="?deletar=<?php echo $p['id']; ?>" class="btn-table btn-del" title="Remover" onclick="return confirm('Remover este produto?')">✕</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </main>
</div>

</body>
</html>