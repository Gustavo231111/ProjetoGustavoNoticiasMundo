<?php
// login.php
session_start();

// Conexão direta (sem arquivo separado para evitar erros)
try {
    $pdo = new PDO("mysql:host=localhost;dbname=portal_mundinho;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("❌ Erro de conexão: " . $e->getMessage() . "<br><br><strong>Verifique se:</strong><br>1. XAMPP está rodando<br>2. Banco 'portal_mundinho' existe<br>3. Rodou o criar_senha.php");
}

// Se já está logado
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_tipo'] === 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: dashboard.php");
    }
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    
    if (empty($email) || empty($senha)) {
        $erro = '⚠️ Preencha email e senha!';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND ativo = 1");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch();
            
            if (!$usuario) {
                $erro = '❌ Email não encontrado!';
            } elseif (!password_verify($senha, $usuario['senha'])) {
                $erro = '❌ Senha incorreta!';
            } else {
                // LOGIN SUCESSO!
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['user_nome'] = $usuario['nome'];
                $_SESSION['user_email'] = $usuario['email'];
                $_SESSION['user_avatar'] = $usuario['avatar'];
                $_SESSION['user_tipo'] = $usuario['tipo'];
                
                // Debug
                echo "<div style='background: #d1fae5; padding: 2rem; text-align: center;'>
                        <h2>✅ LOGIN REALIZADO COM SUCESSO!</h2>
                        <p><strong>Nome:</strong> " . htmlspecialchars($usuario['nome']) . "</p>
                        <p><strong>Tipo:</strong> " . htmlspecialchars($usuario['tipo']) . "</p>
                        <p><strong>Redirecionando...</strong></p>
                      </div>";
                
                // Redirecionar após 2 segundos
                header("Refresh: 2; URL=" . ($usuario['tipo'] === 'admin' ? 'admin.php' : 'dashboard.php'));
                exit;
            }
        } catch (Exception $e) {
            $erro = '❌ Erro: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MundinhoNews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0066cc 0%, #003d7a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-box {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .login-box h2 {
            color: #0066cc;
            font-weight: 800;
            margin-bottom: 0.5rem;
            text-align: center;
        }
        .login-box .subtitle {
            text-align: center;
            color: #64748b;
            margin-bottom: 2rem;
        }
        .form-control {
            padding: 1rem;
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            font-size: 1rem;
        }
        .form-control:focus {
            border-color: #0066cc;
            box-shadow: 0 0 0 3px rgba(0,102,204,0.1);
        }
        .btn-login {
            background: linear-gradient(135deg, #0066cc 0%, #003d7a 100%);
            border: none;
            padding: 1rem;
            border-radius: 10px;
            font-weight: 700;
            width: 100%;
            font-size: 1.1rem;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0,102,204,0.4);
        }
        .erro-box {
            background: #fee2e2;
            color: #991b1b;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            border-left: 4px solid #ef4444;
        }
        .info-box {
            background: #dbeafe;
            color: #1e40af;
            padding: 1.5rem;
            border-radius: 10px;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            border-left: 4px solid #0066cc;
        }
        .info-box strong {
            display: block;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>🗞️ MundinhoNews</h2>
        <p class="subtitle">Faça login para continuar</p>
        
        <?php if ($erro): ?>
            <div class="erro-box">
                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-envelope me-2"></i>Email</label>
                <input type="email" name="email" class="form-control" required 
                       placeholder="seu@email.com" 
                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>
            
            <div class="mb-4">
                <label class="form-label"><i class="fas fa-lock me-2"></i>Senha</label>
                <input type="password" name="senha" class="form-control" required 
                       placeholder="••••••••">
            </div>
            
            <button type="submit" class="btn btn-primary btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>Entrar
            </button>
        </form>
        
        <div class="info-box">
            <i class="fas fa-key me-2"></i><strong>Credenciais de Teste:</strong>
            <strong>👑 Admin:</strong> admin@mundinho.com / admin123
            <strong>👤 Usuário:</strong> teste@mundinho.com / 123456
        </div>
        
        <div class="text-center mt-4">
            <a href="index.php" class="text-decoration-none text-muted">← Voltar ao Início</a>
        </div>
        
        <div class="text-center mt-2">
            <a href="criar_senha.php" class="text-decoration-none text-muted small">🔧 Rodar criador de senhas (primeira vez)</a>
        </div>
    </div>
</body>
</html>