<?php
// nova_noticia.php
require_once 'funcoes.php';
exigirLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = limpar($_POST['titulo'] ?? '');
    $conteudo = trim($_POST['noticia'] ?? '');
    
    if (!$titulo || !$conteudo) {
        mensagem('danger', '⚠️ Título e conteúdo são obrigatórios.');
    } elseif (strlen($titulo) < 5) {
        mensagem('danger', '⚠️ O título deve ter pelo menos 5 caracteres.');
    } else {
        try {
            $pdo = conectar();
            
            $imagem = null;
            if (!empty($_FILES['imagem']['name'])) {
                $imagem = uploadImagem($_FILES['imagem']);
            }
            
            $stmt = $pdo->prepare("INSERT INTO noticias (titulo, noticia, autor, imagem, status) VALUES (?, ?, ?, ?, 'publicada')");
            $stmt->execute([$titulo, $conteudo, $_SESSION['user_id'], $imagem]);
            
            mensagem('success', '✅ Notícia publicada com sucesso!');
            redirecionar('dashboard.php');
        } catch (Exception $e) {
            mensagem('danger', '❌ Erro ao publicar: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Notícia - MundinhoNews</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="container">
        <div class="form-container fade-in">
            <div class="text-center mb-4">
                <i class="fas fa-pen-fancy fa-4x" style="color: var(--azul-primario); margin-bottom: 1rem;"></i>
                <h2 style="font-family: 'Playfair Display', serif; font-weight: 800; color: var(--azul-primario);">
                    Criar Nova Notícia
                </h2>
                <p class="text-muted">Compartilhe sua história com o mundo</p>
            </div>
            
            <?php exibirMensagem(); ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="titulo"><i class="fas fa-heading me-2"></i>Título *</label>
                    <input type="text" id="titulo" name="titulo" required class="form-control" 
                           placeholder="Digite um título chamativo" maxlength="200"
                           value="<?= isset($_POST['titulo']) ? limpar($_POST['titulo']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="imagem"><i class="fas fa-image me-2"></i>Imagem da Notícia (Opcional)</label>
                    <input type="file" id="imagem" name="imagem" class="form-control" accept="image/*">
                    <small class="text-muted">Formatos: JPG, PNG, GIF, WebP</small>
                    <div id="preview" class="mt-3" style="display: none;">
                        <img id="preview-img" src="" alt="Preview" style="max-width: 300px; border-radius: 12px;">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="noticia"><i class="fas fa-newspaper me-2"></i>Conteúdo *</label>
                    <textarea id="noticia" name="noticia" rows="12" required class="form-control"
                              placeholder="Escreva sua notícia aqui..."><?= isset($_POST['noticia']) ? limpar($_POST['noticia']) : '' ?></textarea>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Dica:</strong> Sua notícia será publicada imediatamente na página inicial.
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-lg">
                        <i class="fas fa-paper-plane"></i> Publicar Notícia
                    </button>
                    <a href="dashboard.php" class="btn btn-outline">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </main>
    
    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('imagem').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-img').src = e.target.result;
                    document.getElementById('preview').style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>