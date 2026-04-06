<?php
// admin.php - Painel Principal do Administrador
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=portal_mundinho;charset=utf8mb4", "root", "");

// Estatísticas
$total_usuarios = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE tipo = 'usuario'")->fetchColumn();
$total_noticias = $pdo->query("SELECT COUNT(*) FROM noticias")->fetchColumn();
$total_likes = $pdo->query("SELECT COUNT(*) FROM likes")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin - MundinhoNews</title>
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
        .stat-card {
            background: var(--fundo-card, white);
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
            border-top: 4px solid var(--azul-primario, #0066cc);
        }
        .stat-card h3 {
            color: var(--azul-primario, #0066cc);
            font-size: 2.5rem;
            font-weight: 800;
        }
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
            <h1>👑 Painel Administrativo</h1>
            <p class="mb-0">Bem-vindo, <?= htmlspecialchars($_SESSION['user_nome']) ?>!</p>
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
                    <a href="admin.php" class="ativo"><i class="fas fa-home"></i> Início</a>
                    <a href="admin_usuarios.php"><i class="fas fa-users"></i> Usuários</a>
                    <a href="admin_noticias.php"><i class="fas fa-newspaper"></i> Notícias</a>
                    <a href="index.php"><i class="fas fa-globe"></i> Ver Site</a>
                    <a href="logout.php" style="color: #ef4444;"><i class="fas fa-sign-out-alt"></i> Sair</a>
                </div>
                
                <div class="stat-card">
                    <h3>👑</h3>
                    <p class="text-muted">Administrador</p>
                </div>
            </div>
            
            <div class="col-lg-9">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h3><?= $total_usuarios ?></h3>
                            <p class="text-muted">Usuários</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h3><?= $total_noticias ?></h3>
                            <p class="text-muted">Notícias</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h3><?= $total_likes ?></h3>
                            <p class="text-muted">Curtidas</p>
                        </div>
                    </div>
                </div>
                
                <div class="content-box mt-4">
                    <h3><i class="fas fa-tachometer-alt me-2"></i>Visão Geral</h3>
                    <p class="text-muted">Bem-vindo ao painel administrativo! Use o menu ao lado para gerenciar usuários e notícias do MundinhoNews.</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card h-100 border-0" style="background: var(--azul-suave, #e6f0ff);">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-users me-2"></i>Gerenciar Usuários</h5>
                                    <p class="card-text">Crie, edite ou exclua usuários do sistema.</p>
                                    <a href="admin_usuarios.php" class="btn btn-primary">Acessar</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 border-0" style="background: var(--azul-suave, #e6f0ff);">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-newspaper me-2"></i>Gerenciar Notícias</h5>
                                    <p class="card-text">Veja, edite ou exclua notícias de todos os usuários.</p>
                                    <a href="admin_noticias.php" class="btn btn-primary">Acessar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>