<?php
// admin_usuarios.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=portal_mundinho;charset=utf8mb4", "root", "");
$usuarios = $pdo->query("SELECT * FROM usuarios ORDER BY data_cadastro DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - Admin</title>
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
        .badge-admin {
            background: linear-gradient(135deg, #0066cc 0%, #003d7a 100%);
            color: white;
            padding: 0.3rem 1rem;
            border-radius: 20px;
        }
        .badge-user {
            background: #e2e8f0;
            color: #475569;
            padding: 0.3rem 1rem;
            border-radius: 20px;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="admin-header">
        <div class="container">
            <h1>👥 Gerenciar Usuários</h1>
            <p class="mb-0">Crie, edite e exclua usuários do sistema</p>
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
                    <a href="admin_usuarios.php" class="ativo"><i class="fas fa-users"></i> Usuários</a>
                    <a href="admin_noticias.php"><i class="fas fa-newspaper"></i> Notícias</a>
                    <a href="index.php"><i class="fas fa-globe"></i> Ver Site</a>
                    <a href="logout.php" style="color: #ef4444;"><i class="fas fa-sign-out-alt"></i> Sair</a>
                </div>
            </div>
            
            <div class="col-lg-9">
                <div class="content-box">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3><i class="fas fa-users me-2"></i>Todos os Usuários</h3>
                        <a href="admin_criar_usuario.php" class="btn btn-success">
                            <i class="fas fa-user-plus me-2"></i>Novo Usuário
                        </a>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuário</th>
                                    <th>Email</th>
                                    <th>Tipo</th>
                                    <th>Cadastro</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $user): ?>
                                    <tr>
                                        <td><?= $user['id'] ?></td>
                                        <td>
                                            <img src="<?= htmlspecialchars($user['avatar']) ?>" 
                                                 style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;">
                                            <?= htmlspecialchars($user['nome']) ?>
                                        </td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td>
                                            <?php if ($user['tipo'] === 'admin'): ?>
                                                <span class="badge-admin">👑 Admin</span>
                                            <?php else: ?>
                                                <span class="badge-user">👤 Usuário</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($user['data_cadastro'])) ?></td>
                                        <td>
                                            <a href="admin_editar_usuario.php?id=<?= $user['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                <a href="admin_excluir_usuario.php?id=<?= $user['id'] ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('⚠️ Tem certeza que deseja excluir este usuário?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>