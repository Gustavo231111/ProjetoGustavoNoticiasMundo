<?php
// admin_criar_usuario.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=portal_mundinho;charset=utf8mb4", "root", "");
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $tipo = $_POST['tipo'];
    
    if (empty($nome) || empty($email) || empty($senha)) {
        $erro = 'Preencha todos os campos obrigatórios!';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter pelo menos 6 caracteres!';
    } elseif ($senha !== $confirmar_senha) {
        $erro = 'As senhas não coincidem!';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $erro = 'Este email já está cadastrado!';
            } else {
                $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, avatar, tipo) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $nome,
                    $email,
                    password_hash($senha, PASSWORD_DEFAULT),
                    'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($nome),
                    $tipo
                ]);
                header("Location: admin_usuarios.php?msg=Usuário criado com sucesso!&tipo=success");
                exit;
            }
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
    <title>Criar Usuário - Admin</title>
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
            <h1>➕ Criar Novo Usuário</h1>
            <p class="mb-0">Adicione um novo usuário ao sistema</p>
        </div>
    </div>
    
    <div class="container">
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
                    <?php if ($erro): ?>
                        <div class="alert alert-danger"><?= $erro ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-user me-2"></i>Nome *</label>
                                    <input type="text" name="nome" class="form-control" required 
                                           value="<?= isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-envelope me-2"></i>Email *</label>
                                    <input type="email" name="email" class="form-control" required 
                                           value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-lock me-2"></i>Senha *</label>
                                    <input type="password" name="senha" class="form-control" required minlength="6">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-lock me-2"></i>Confirmar Senha *</label>
                                    <input type="password" name="confirmar_senha" class="form-control" required minlength="6">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-id-badge me-2"></i>Tipo de Usuário *</label>
                            <select name="tipo" class="form-select">
                                <option value="usuario">👤 Usuário Comum</option>
                                <option value="admin">👑 Administrador</option>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Criar Usuário
                            </button>
                            <a href="admin_usuarios.php" class="btn btn-outline-secondary btn-lg">
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