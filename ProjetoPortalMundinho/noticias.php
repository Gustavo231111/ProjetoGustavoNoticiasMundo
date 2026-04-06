<?php
require_once 'funcoes.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) redirecionar('index.php');

try {
    $pdo = conectar();
    
    // Buscar notícia
    $stmt = $pdo->prepare("
        SELECT n.*, u.nome as autor_nome, u.avatar as autor_avatar
        FROM noticias n 
        INNER JOIN usuarios u ON n.autor = u.id 
        WHERE n.id = ? AND n.status = 'publicada'
    ");
    $stmt->execute([$id]);
    $noticia = $stmt->fetch();
    
    if (!$noticia) {
        mensagem('danger', 'Notícia não encontrada.');
        redirecionar('index.php');
    }
    
    // Buscar comentários
    $stmt = $pdo->prepare("
        SELECT c.*, u.nome, u.avatar 
        FROM comentarios c 
        INNER JOIN usuarios u ON c.usuario_id = u.id 
        WHERE c.noticia_id = ? 
        ORDER BY c.data DESC
    ");
    $stmt->execute([$id]);
    $comentarios = $stmt->fetchAll();
    
    // Contar comentários
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM comentarios WHERE noticia_id = ?");
    $stmt->execute([$id]);
    $total_comentarios = $stmt->fetch()['total'];
    
} catch (Exception $e) {
    mensagem('danger', 'Erro ao carregar notícia.');
    redirecionar('index.php');
}

// Processar comentário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $conteudo = limpar($_POST['comentario'] ?? '');
    
    if ($conteudo && strlen($conteudo) >= 3) {
        try {
            $stmt = $pdo->prepare("INSERT INTO comentarios (noticia_id, usuario_id, conteudo) VALUES (?, ?, ?)");
            $stmt->execute([$id, $_SESSION['user_id'], $conteudo]);
            
            mensagem('success', '✅ Comentário publicado!');
            redirecionar('noticia.php?id=' . $id);
        } catch (Exception $e) {
            mensagem('danger', '❌ Erro ao publicar comentário.');
        }
    } else {
        mensagem('danger', '⚠️ O comentário deve ter pelo menos 3 caracteres.');
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= limpar($noticia['titulo']) ?> - MundinhoNews</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="container">
        <?php exibirMensagem(); ?>
        
        <article class="noticia-completa fade-in">
            <?php if ($noticia['imagem'] && file_exists($noticia['imagem'])): ?>
                <img src="<?= limpar($noticia['imagem']) ?>" alt="<?= limpar($noticia['titulo']) ?>" class="img-fluid">
            <?php else: ?>
                <img src="https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800" alt="Notícia" class="img-fluid">
            <?php endif; ?>
            
            <h1><?= limpar($noticia['titulo']) ?></h1>
            
            <div class="autor-info">
                <img src="<?= $noticia['autor_avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=Default' ?>" 
                     alt="Avatar" 
                     style="width: 60px; height: 60px; border-radius: 50%; border: 3px solid var(--azul-primario);">
                <div>
                    <strong style="font-size: 1.1rem; color: var(--azul-escuro);"><?= limpar($noticia['autor_nome']) ?></strong>
                    <br>
                    <small style="color: var(--texto-secundario);">
                        <i class="far fa-clock"></i> Publicado em <?= formatarData($noticia['data']) ?>
                    </small>
                </div>
            </div>
            
            <div class="conteudo">
                <?= nl2br(limpar($noticia['noticia'])) ?>
            </div>
            
            <div class="social-links" style="justify-content: flex-start; margin-top: 2rem;">
                <span style="margin-right: 1rem; color: var(--texto-secundario);">Compartilhar:</span>
                <a href="https://facebook.com/sharer.php?u=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="social-link" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <a href="https://twitter.com/intent/tweet?url=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="social-link" target="_blank"><i class="fab fa-twitter"></i></a>
                <a href="https://wa.me/?text=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="social-link" target="_blank"><i class="fab fa-whatsapp"></i></a>
            </div>
            
            <hr style="margin: 3rem 0; border-color: var(--borda);">
            
            <a href="index.php" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Voltar para as Notícias
            </a>
        </article>
        
        <!-- SEÇÃO DE COMENTÁRIOS -->
        <section class="comentarios-section fade-in">
            <h2 style="font-family: 'Playfair Display', serif; color: var(--azul-primario); margin-bottom: 1.5rem;">
                💬 Comentários (<?= $total_comentarios ?>)
            </h2>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="comentario-form">
                    <h4 style="margin-bottom: 1rem; color: var(--texto);">Deixe seu comentário:</h4>
                    <form method="POST">
                        <div class="form-group">
                            <textarea name="comentario" rows="4" class="form-control" 
                                      placeholder="Escreva seu comentário aqui... Seja respeitoso!" required></textarea>
                        </div>
                        <button type="submit" class="btn">
                            <i class="fas fa-paper-plane"></i> Publicar Comentário
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Precisa de conta para comentar!</strong> 
                    <a href="login.php" style="color: var(--azul-primario); font-weight: 600;">Faça login</a> ou 
                    <a href="cadastro.php" style="color: var(--azul-primario); font-weight: 600;">crie uma conta grátis</a>.
                </div>
            <?php endif; ?>
            
            <div style="margin-top: 2rem;">
                <?php if (empty($comentarios)): ?>
                    <p style="text-align: center; color: var(--texto-secundario); padding: 2rem;">
                        <i class="far fa-comment-dots fa-3x mb-3" style="opacity: 0.3;"></i>
                        <br>Seja o primeiro a comentar esta notícia!
                    </p>
                <?php else: ?>
                    <?php foreach ($comentarios as $comentario): ?>
                        <div class="comentario-card">
                            <div class="comentario-header">
                                <img src="<?= $comentario['avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=Default' ?>" 
                                     alt="Avatar" 
                                     class="comentario-avatar">
                                <div>
                                    <div class="comentario-autor"><?= limpar($comentario['nome']) ?></div>
                                    <div class="comentario-data"><?= formatarData($comentario['data']) ?></div>
                                </div>
                            </div>
                            <p class="comentario-texto"><?= nl2br(limpar($comentario['conteudo'])) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>
    
    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>