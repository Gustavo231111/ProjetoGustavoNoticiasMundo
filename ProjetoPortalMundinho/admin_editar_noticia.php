<?php
// admin_editar_noticia.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=portal_mundinho;charset=utf8mb4", "root", "");
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) header("Location: admin_noticias.php");

$stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
$stmt->execute([$id]);
$noticia = $stmt->fetch();

if (!$noticia) header("Location: admin_noticias.php");

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $conteudo = trim($_POST['noticia']);
    
    if (empty($titulo) || empty($conteudo)) {
        $erro = 'Preencha título e conteúdo!';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE noticias SET titulo = ?, noticia = ? WHERE id = ?");
            $stmt->execute([$titulo, $conteudo, $id]);
            header("Location: admin_noticias.php?msg=Notícia atualizada!&tipo=success");
            exit;
        } catch (Exception $e) {
            $erro = 'Erro: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Notícia - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: var(--fundo, #f0f2f5); }
        .admin-header {
            background: var(--gradiente-hero, linear-gradient(135deg, #0066cc 0%, #003d7a 100%));
            color: white;
            padding: 3rem 2rem;
            margin-bottom: 2rem;
        }
        .admin-nav {
            background: var(--fundo-card, white);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .admin-nav a {
            display: block;
            padding: 1rem 1.5rem;
            color: var(--texto, #1a1a2e);
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 0.5rem;
            transition: all 0.3s;
            font-weight: 600;
        }
        .admin-nav a:hover, .admin-nav a.ativo {
            background: var(--azul-suave, #e6f0ff);
            color: var(--azul-primario, #0066cc);
        }
        .admin-nav a i { width: 25px; margin-right: 0.5rem; }
        .content-box {
            background: var(--fundo-card, white);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="admin-header">
        <div class="container">
            <h1>✏️ Editar Notícia</h1>
            <p class="mb-0">Edite o conteúdo da notícia</p>
        </div>
    </div>
    
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="admin-nav">
                    <a href="admin.php"><i class="fas fa-home"></i> Início</a>
                    <a href="admin_usuarios.php"><i class="fas fa-users"></i> Usuários</a>
                    <a href="admin_noticias.php" class="ativo"><i class="fas fa-newspaper"></i> Notícias</a>
                    <a href="index.php"><i class="fas fa-globe"></i> Ver Site</a>
                    <a href="logout.php" style="color: #ef4444;"><i class="fas fa-sign-out-alt"></i> Sair</a>
                </div>
            </div>
            
            <div class="col-lg-9">
                <div class="content-box">
                    <?php if ($erro): ?>
                        <div class="alert alert-danger"><?= $erro ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-heading me-2"></i>Título *</label>
                            <input type="text" name="titulo" class="form-control" required 
                                   value="<?= htmlspecialchars($noticia['titulo']) ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-newspaper me-2"></i>Conteúdo *</label>
                            <textarea name="noticia" class="form-control" rows="10" required><?= htmlspecialchars($noticia['noticia']) ?></textarea>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Salvar Alterações
                            </button>
                            <a href="admin_noticias.php" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>