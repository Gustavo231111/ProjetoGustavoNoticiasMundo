<?php
// admin_noticias.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=portal_mundinho;charset=utf8mb4", "root", "");

$noticias = $pdo->query("
    SELECT n.*, u.nome as autor_nome, u.avatar as autor_avatar,
           (SELECT COUNT(*) FROM likes WHERE noticia_id = n.id) as total_likes
    FROM noticias n
    INNER JOIN usuarios u ON n.autor = u.id
    ORDER BY n.data DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Notícias - Admin</title>
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
        .noticia-item {
            border: 1px solid var(--borda, #e2e8f0);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s;
        }
        .noticia-item:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .noticia-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="admin-header">
        <div class="container">
            <h1>📰 Gerenciar Notícias</h1>
            <p class="mb-0">Veja, edite e exclua notícias de todos os usuários</p>
        </div>
    </div>
    
    <div class="container">
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-<?= htmlspecialchars($_GET['tipo']) ?> alert-dismissible fade show">
                <?= htmlspecialchars($_GET['msg']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
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
                    <h3><i class="fas fa-newspaper me-2"></i>Todas as Notícias (<?= count($noticias) ?>)</h3>
                    
                    <?php if (empty($noticias)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Nenhuma notícia publicada ainda.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive mt-4">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Notícia</th>
                                        <th>Autor</th>
                                        <th>Data</th>
                                        <th>Curtidas</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($noticias as $noticia): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($noticia['titulo']) ?></strong>
                                            </td>
                                            <td>
                                                <img src="<?= htmlspecialchars($noticia['autor_avatar']) ?>" 
                                                     style="width: 30px; height: 30px; border-radius: 50%; margin-right: 8px;">
                                                <?= htmlspecialchars($noticia['autor_nome']) ?>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($noticia['data'])) ?></td>
                                            <td>
                                                <i class="fas fa-heart text-danger"></i> 
                                                <?= $noticia['total_likes'] ?>
                                            </td>
                                            <td>
                                                <a href="noticia.php?id=<?= $noticia['id'] ?>" 
                                                   class="btn btn-sm btn-outline-info" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="admin_editar_noticia.php?id=<?= $noticia['id'] ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="admin_excluir_noticia.php?id=<?= $noticia['id'] ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('⚠️ Tem certeza que deseja excluir esta notícia?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>