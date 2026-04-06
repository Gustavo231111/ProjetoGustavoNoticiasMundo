<?php
require_once 'funcoes.php';

if (isset($_SESSION['user_id'])) {
    redirecionar('dashboard.php');
}

$avatares = [
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Alex',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Sam',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Jordan',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Taylor',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Morgan',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Casey',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Riley',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Quinn',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Avery',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Blake',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Drew',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Reese',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Jamie',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Charlie',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Pat',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Robin',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Skyler',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Dakota',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Peyton',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Hayden',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Emerson',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Finley',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Rowan',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Phoenix',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=River',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Sage',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Winter',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Summer',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Autumn',
    'https://api.dicebear.com/7.x/avataaars/svg?seed=Spring',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = limpar($_POST['nome'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    $avatar = limpar($_POST['avatar'] ?? $avatares[0]);
    
    if (!$nome || !$email || !$senha) {
        mensagem('danger', '⚠️ Preencha todos os campos obrigatórios.');
    } elseif (strlen($senha) < 6) {
        mensagem('danger', '⚠️ A senha deve ter pelo menos 6 caracteres.');
    } elseif ($senha !== $confirmar_senha) {
        mensagem('danger', '⚠️ As senhas não coincidem.');
    } else {
        try {
            $pdo = conectar();
            
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                mensagem('danger', '⚠️ Este email já está cadastrado.');
            } else {
                $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, avatar) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nome, $email, password_hash($senha, PASSWORD_DEFAULT), $avatar]);
                
                mensagem('success', '✅ Conta criada! Faça login para começar.');
                redirecionar('login.php');
            }
        } catch (Exception $e) {
            mensagem('danger', '❌ Erro ao cadastrar.');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta - MundinhoNews</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="cadastro-page">
        <div class="container">
            <div class="cadastro-card fade-in">
                <div class="cadastro-header">
                    <i class="fas fa-user-plus fa-4x mb-3"></i>
                    <h2 style="font-family: 'Playfair Display', serif; font-weight: 800;">Crie Sua Conta Grátis</h2>
                    <p class="mb-0 opacity-75">Junte-se a nossa comunidade</p>
                </div>
                <div class="cadastro-body">
                    <?php exibirMensagem(); ?>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label for="nome">
                                <i class="fas fa-user me-2" style="color: var(--azul-primario);"></i>Nome Completo *
                            </label>
                            <input type="text" id="nome" name="nome" required class="form-control" 
                                   placeholder="Ex: João Silva"
                                   value="<?= isset($_POST['nome']) ? limpar($_POST['nome']) : '' ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">
                                <i class="fas fa-envelope me-2" style="color: var(--azul-primario);"></i>Email *
                            </label>
                            <input type="email" id="email" name="email" required class="form-control"
                                   placeholder="seu@email.com"
                                   value="<?= isset($_POST['email']) ? limpar($_POST['email']) : '' ?>">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="senha">
                                        <i class="fas fa-lock me-2" style="color: var(--azul-primario);"></i>Senha *
                                    </label>
                                    <input type="password" id="senha" name="senha" required class="form-control"
                                           placeholder="Mínimo 6 caracteres">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="confirmar_senha">
                                        <i class="fas fa-lock me-2" style="color: var(--azul-primario);"></i>Confirmar *
                                    </label>
                                    <input type="password" id="confirmar_senha" name="confirmar_senha" required class="form-control"
                                           placeholder="Repita a senha">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label style="color: var(--texto);">
                                <i class="fas fa-id-card me-2" style="color: var(--azul-primario);"></i>Escolha Seu Avatar *
                            </label>
                            <div class="avatar-selection">
                                <?php foreach ($avatares as $i => $avatar): ?>
                                    <div class="avatar-option <?= $i === 0 ? 'selected' : '' ?>" 
                                         data-avatar="<?= $avatar ?>"
                                         onclick="selectAvatar(this)">
                                        <img src="<?= $avatar ?>" alt="Avatar <?= $i + 1 ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" id="avatar_selecionado" name="avatar" value="<?= $avatares[0] ?>">
                            <small class="text-muted" style="color: var(--texto-secundario);">Clique em um avatar para selecionar</small>
                        </div>
                        
                        <button type="submit" class="btn w-100 btn-lg">
                            <i class="fas fa-user-plus"></i> Criar Minha Conta
                        </button>
                    </form>
                    
                    <hr class="my-4" style="border-color: var(--borda);">
                    
                    <div class="text-center">
                        <p class="mb-2" style="color: var(--texto-secundario);">Já tem uma conta?</p>
                        <a href="login.php" class="btn btn-outline w-100">
                            <i class="fas fa-sign-in-alt"></i> Fazer Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectAvatar(element) {
            document.querySelectorAll('.avatar-option').forEach(opt => opt.classList.remove('selected'));
            element.classList.add('selected');
            document.getElementById('avatar_selecionado').value = element.dataset.avatar;
        }
    </script>
</body>
</html>