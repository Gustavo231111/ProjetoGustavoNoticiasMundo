<?php
// admin_editar_usuario.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=portal_mundinho;charset=utf8mb4", "root", "");
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) header("Location: admin_usuarios.php");

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch();

if (!$usuario) header("Location: admin_usuarios.php");

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $tipo = $_POST['tipo'];
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    $senha = $_POST['senha'];
    
    if (empty($nome) || empty($email)) {
        $erro = 'Preencha nome e email!';
    } else {
        try {
            if (!empty($senha)) {
                $stmt = $pdo->prepare("UPDATE usuarios SET nome=?, email=?, tipo=?, ativo=?, senha=? WHERE id=?");
                $stmt->execute([$nome, $email, $tipo, $ativo, password_hash($senha, PASSWORD_DEFAULT), $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE usuarios SET nome=?, email=?, tipo=?, ativo=? WHERE id=?");
                $stmt->execute([$nome, $email, $tipo, $ativo, $id]);
            }
            header("Location: admin_usuarios.php?msg=Usuário atualizado!&tipo=success");
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
    <title>Editar Usuário - Admin</title>
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
            <h1>✏️ Editar Usuário</h1>
            <p class="mb-0">Edite as informações do usuário</p>
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
                        <div class="text-center mb-4">
                            <img src="<?= htmlspecialchars($usuario['avatar']) ?>" 
                                 style="width: 100px; height: 100px; border-radius: 50%; border: 4px solid var(--azul-primario, #0066cc);">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-user me-2"></i>Nome *</label>
                            <input type="text" name="nome" class="form-control" 
                                   value="<?= htmlspecialchars($usuario['nome']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-envelope me-2"></i>Email *</label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?= htmlspecialchars($usuario['email']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-id-badge me-2"></i>Tipo *</label>
                            <select name="tipo" class="form-select">
                                <option value="usuario" <?= $usuario['tipo'] === 'usuario' ? 'selected' : '' ?>>👤 Usuário</option>
                                <option value="admin" <?= $usuario['tipo'] === 'admin' ? 'selected' : '' ?>>👑 Admin</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-lock me-2"></i>Nova Senha (opcional)</label>
                            <input type="password" name="senha" class="form-control" 
                                   placeholder="Deixe em branco para manter a atual">
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="ativo" class="form-check-input" 
                                   id="ativo" <?= $usuario['ativo'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="ativo">Usuário Ativo</label>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Salvar
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