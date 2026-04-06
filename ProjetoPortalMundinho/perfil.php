<?php
require_once 'funcoes.php';
exigirLogin();

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

try {
    $pdo = conectar();
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $usuario = $stmt->fetch();
} catch (Exception $e) {
    redirecionar('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = limpar($_POST['nome'] ?? '');
    $avatar = limpar($_POST['avatar'] ?? $usuario['avatar']);
    $senha_atual = $_POST['senha_atual'] ?? '';
    $nova_senha = $_POST['nova_senha'] ?? '';
    
    if (!$nome) {
        mensagem('danger', '⚠️ Nome é obrigatório.');
    } else {
        try {
            if ($nova_senha && strlen($nova_senha) >= 6) {
                if (password_verify($senha_atual, $usuario['senha'])) {
                    $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, avatar = ?, senha = ? WHERE id = ?");
                    $stmt->execute([$nome, $avatar, password_hash($nova_senha, PASSWORD_DEFAULT), $_SESSION['user_id']]);
                    $_SESSION['user_nome'] = $nome;
                    $_SESSION['user_avatar'] = $avatar;
                    mensagem('success', '✅ Perfil e senha atualizados no banco de dados!');
                } else {
                    mensagem('danger', '⚠️ Senha atual incorreta.');
                }
            } else {
                $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, avatar = ? WHERE id = ?");
                $stmt->execute([$nome, $avatar, $_SESSION['user_id']]);
                $_SESSION['user_nome'] = $nome;
                $_SESSION['user_avatar'] = $avatar;
                mensagem('success', '✅ Perfil atualizado no banco de dados!');
            }
        } catch (Exception $e) {
            mensagem('danger', '❌ Erro ao atualizar: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - MundinhoNews</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <section class="profile-hero">
        <h1>👤 Meu Perfil</h1>
        <p class="lead">Personalize sua conta e avatar</p>
    </section>
    
    <main class="container">
        <?php exibirMensagem(); ?>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="profile-section fade-in">
                    <div class="text-center mb-4">
                        <div class="profile-avatar" style="width: 150px; height: 150px; margin-bottom: 1.5rem;">
                            <img src="<?= $usuario['avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . $usuario['id'] ?>" alt="Avatar">
                        </div>
                        <h2 style="font-family: 'Playfair Display', serif; color: var(--azul-primario);"><?= limpar($usuario['nome']) ?></h2>
                        <p class="text-muted"><?= limpar($usuario['email']) ?></p>
                    </div>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label for="nome"><i class="fas fa-user me-2"></i>Nome *</label>
                            <input type="text" id="nome" name="nome" required class="form-control" 
                                   value="<?= limpar($usuario['nome']) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope me-2"></i>Email</label>
                            <input type="email" id="email" class="form-control" value="<?= limpar($usuario['email']) ?>" disabled>
                            <small class="text-muted">Email não pode ser alterado</small>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-id-card me-2"></i>Escolha Seu Avatar</label>
                            <div class="avatar-selection">
                                <?php foreach ($avatares as $avatar): ?>
                                    <div class="avatar-option <?= $usuario['avatar'] === $avatar ? 'selected' : '' ?>" 
                                         data-avatar="<?= $avatar ?>"
                                         onclick="selectAvatar(this)">
                                        <img src="<?= $avatar ?>" alt="Avatar">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" id="avatar_selecionado" name="avatar" value="<?= $usuario['avatar'] ?>">
                        </div>
                        
                        <hr class="my-4">
                        
                        <h4 class="mb-3" style="color: var(--azul-primario); font-family: 'Playfair Display', serif;">
                            <i class="fas fa-lock me-2"></i>Alterar Senha (Opcional)
                        </h4>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Nota:</strong> A senha será atualizada diretamente no banco de dados.
                        </div>
                        
                        <div class="form-group">
                            <label for="senha_atual"><i class="fas fa-key me-2"></i>Senha Atual</label>
                            <input type="password" id="senha_atual" name="senha_atual" class="form-control"
                                   placeholder="Necessário para mudar a senha">
                        </div>
                        
                        <div class="form-group">
                            <label for="nova_senha"><i class="fas fa-lock me-2"></i>Nova Senha</label>
                            <input type="password" id="nova_senha" name="nova_senha" class="form-control"
                                   placeholder="Deixe em branco para manter">
                        </div>
                        
                        <button type="submit" class="btn btn-lg w-100">
                            <i class="fas fa-database"></i> Salvar no Banco de Dados
                        </button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <a href="dashboard.php" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Voltar ao Painel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
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