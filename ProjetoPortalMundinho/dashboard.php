<?php
// dashboard.php
require_once 'funcoes.php';
exigirLogin();

try {
    $pdo = conectar();
    $user_id = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("SELECT * FROM noticias WHERE autor = ? ORDER BY data DESC");
    $stmt->execute([$user_id]);
    $minhas_noticias = $stmt->fetchAll();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM noticias WHERE autor = ?");
    $stmt->execute([$user_id]);
    $total_noticias = $stmt->fetch()['total'];
} catch (Exception $e) {
    $minhas_noticias = [];
    $total_noticias = 0;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - MundinhoNews</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="container">
        <?php exibirMensagem(); ?>
        
        <div class="hero fade-in">
            <h1>👋 Olá, <?= limpar($_SESSION['user_nome']) ?>!</h1>
            <p>Bem-vindo ao seu painel de jornalista cidadão</p>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="stat-card fade-in">
                    <i class="fas fa-newspaper fa-3x" style="color: var(--azul-primario); margin-bottom: 1rem;"></i>
                    <h3><?= $total_noticias ?></h3>
                    <p class="text-muted">Notícias Publicadas</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card fade-in">
                    <i class="fas fa-user fa-3x" style="color: var(--azul-primario); margin-bottom: 1rem;"></i>
                    <h3><?= limpar($_SESSION['user_nome']) ?></h3>
                    <p class="text-muted">Seu Perfil</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card fade-in">
                    <i class="fas fa-calendar-check fa-3x" style="color: var(--azul-primario); margin-bottom: 1rem;"></i>
                    <h3>Ativo</h3>
                    <p class="text-muted">Status da Conta</p>
                </div>
            </div>
        </div>
        
        <div class="dashboard-header fade-in">
            <h3 style="font-family: 'Playfair Display', serif; font-weight: 700; color: var(--azul-primario);">
                📝 Minhas Notícias
            </h3>
            <div class="d-flex gap-2 flex-wrap">
                <a href="nova_noticia.php" class="btn">
                    <i class="fas fa-plus"></i> Nova Notícia
                </a>
                <a href="perfil.php" class="btn btn-outline">
                    <i class="fas fa-user-cog"></i> Meu Perfil
                </a>
            </div>
        </div>
        
        <?php if (empty($minhas_noticias)): ?>
            <div class="alert alert-info fade-in">
                <i class="fas fa-info-circle"></i>
                Você ainda não publicou nenhuma notícia. Que tal começar agora?
            </div>
            <div class="text-center py-5 fade-in">
                <i class="fas fa-newspaper fa-5x text-muted mb-4" style="opacity: 0.3;"></i>
                <p class="lead">Sua primeira notícia está te esperando!</p>
                <a href="nova_noticia.php" class="btn btn-lg">
                    <i class="fas fa-pen-fancy"></i> Criar Primeira Notícia
                </a>
            </div>
        <?php else: ?>
            <div class="tabela-responsive fade-in">
                <table>
                    <thead>
                        <tr>
                            <th><i class="fas fa-heading"></i> Título</th>
                            <th><i class="fas fa-calendar"></i> Data</th>
                            <th><i class="fas fa-tag"></i> Status</th>
                            <th><i class="fas fa-cog"></i> Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($minhas_noticias as $noticia): ?>
                            <tr>
                                <td>
                                    <a href="noticia.php?id=<?= $noticia['id'] ?>" target="_blank" style="color: var(--azul-primario); font-weight: 600;">
                                        <?= limpar(resumir($noticia['titulo'], 50)) ?>
                                    </a>
                                </td>
                                <td><?= formatarData($noticia['data']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $noticia['status'] === 'publicada' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst($noticia['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="noticia.php?id=<?= $noticia['id'] ?>" class="btn btn-sm btn-outline" target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="editar_noticia.php?id=<?= $noticia['id'] ?>" class="btn btn-sm btn-outline">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="excluir_noticia.php?id=<?= $noticia['id'] ?>" class="btn btn-sm btn-danger"
                                           onclick="return confirm('Tem certeza que deseja excluir?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
    
    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>