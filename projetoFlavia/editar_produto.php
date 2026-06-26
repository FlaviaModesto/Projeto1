<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

include 'conexao.php';

$nome_usuario = $_SESSION['usuario'];
$inicial = strtoupper(mb_substr($nome_usuario, 0, 1));
$mensagem = "";
$tipo = "";

// Valida o ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: pesquisar_produto.php");
    exit;
}

$id = (int) $_GET['id'];

// Busca o produto
$stmt = $conexao->prepare("SELECT id, nome, descricao, preco, quantidade FROM produtos WHERE id = ?");
$stmt->execute([$id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    header("Location: pesquisar_produto.php");
    exit;
}

// Processa o formulário de edição
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['salvar'])) {

    $nome      = trim($_POST['nome_produto']);
    $descricao = trim($_POST['descricao']);
    $preco     = $_POST['preco'];
    $quantidade = (int) $_POST['quantidade'];

    if (empty($nome) || empty($preco) || $quantidade < 0) {
        $mensagem = "❌ Preencha todos os campos obrigatórios.";
        $tipo = "erro";
    } else {
        $upd = $conexao->prepare("UPDATE produtos SET nome = ?, descricao = ?, preco = ?, quantidade = ? WHERE id = ?");

        if ($upd->execute([$nome, $descricao, $preco, $quantidade, $id])) {
            // Atualiza os dados exibidos no form
            $produto['nome']       = $nome;
            $produto['descricao']  = $descricao;
            $produto['preco']      = $preco;
            $produto['quantidade'] = $quantidade;

            $mensagem = "✅ Produto atualizado com sucesso!";
            $tipo = "sucesso";
        } else {
            $mensagem = "❌ Erro ao atualizar produto.";
            $tipo = "erro";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Editar Produto</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <style>
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.85rem 1.5rem;
            font-family: var(--font-display);
            font-size: 0.88rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            border: 1px solid var(--border-violet);
            border-radius: var(--radius-md);
            cursor: pointer;
            text-decoration: none;
            background: transparent;
            color: var(--violet-light);
            transition: all var(--transition);
            margin-top: 0.5rem;
            width: 100%;
        }
        .btn-secondary:hover {
            background: rgba(124,58,237,0.15);
            color: var(--text-white);
        }
        .form-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-top: 0.25rem;
        }
        .form-actions .btn,
        .form-actions .btn-secondary {
            margin-top: 0;
        }
        .product-id-badge {
            display: inline-block;
            padding: 0.2rem 0.65rem;
            background: rgba(124,58,237,0.12);
            border: 1px solid var(--border-violet);
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            color: var(--violet-light);
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body class="dashboard-body">

<div class="dashboard-layout">

    <!-- ── SIDEBAR ── -->
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

        <a href="logout.php" class="nav-item nav-logout">
            <span class="nav-icon">⏻</span>
            Sair da conta
        </a>

    </aside>

    <!-- ── CONTEÚDO ── -->
    <main class="dashboard-main">

        <div class="dash-header">
            <div>
                <div class="badge">✦ Estoque</div>
                <h1>Editar <span>Produto</span></h1>
                <p class="subtitle">Atualize as informações do produto selecionado.</p>
            </div>
        </div>

        <div class="form-card">

            <span class="product-id-badge">ID #<?php echo $produto['id']; ?></span>

            <?php if ($mensagem !== ""): ?>
                <p class="msg msg-<?php echo $tipo; ?>"><?php echo $mensagem; ?></p>
            <?php endif; ?>

            <form method="POST" action="" autocomplete="off">

                <div>
                    <label>Nome do produto *</label>
                    <input type="text" name="nome_produto" required
                           placeholder="Ex: Camiseta Básica"
                           value="<?php echo htmlspecialchars($produto['nome']); ?>">
                </div>

                <div>
                    <label>Descrição</label>
                    <textarea name="descricao" placeholder="Descreva o produto..." rows="3"><?php echo htmlspecialchars($produto['descricao'] ?? ''); ?></textarea>
                </div>

                <div class="form-row">
                    <div>
                        <label>Preço (R$) *</label>
                        <input type="number" name="preco" required
                               placeholder="0,00" step="0.01" min="0"
                               value="<?php echo htmlspecialchars($produto['preco']); ?>">
                    </div>
                    <div>
                        <label>Quantidade *</label>
                        <input type="number" name="quantidade" required
                               placeholder="0" min="0"
                               value="<?php echo htmlspecialchars($produto['quantidade']); ?>">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" name="salvar" class="btn">Salvar →</button>
                    <a href="pesquisar_produto.php" class="btn-secondary">← Voltar</a>
                </div>

            </form>

        </div>

    </main>

</div>

</body>
</html>
