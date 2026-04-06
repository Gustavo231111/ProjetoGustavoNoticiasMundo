<?php
// editar_noticia.php
session_start();
require_once 'conexao.php';
require_once 'funcoes.php';
require_once 'verifica_login.php';

exigirLogin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) redirecionar('dashboard.php');

$pdo = conectar();
$stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ? AND autor = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$noticia = $stmt->fetch();

if (!$noticia && !isAdmin()) {
    mensagem('danger', 'Notícia não encontrada ou sem permissão.');
    redirecionar('dashboard.php');
}

// Se for admin, pode editar qualquer notícia
if (!$noticia && isAdmin()) {
    $stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
    $stmt->execute([$id]);
    $noticia = $stmt->fetch();
}

if (!$noticia) {
    mensagem('danger', 'Notícia não encontrada.');
    redirecionar('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = limpar($_POST['titulo'] ?? '');
    $conteudo = trim($_POST['noticia'] ?? '');
    
    if (!$titulo || !$conteudo) {
        mensagem('danger', 'Título e conteúdo são obrigatórios.');
    } else {
        $imagem = $noticia['imagem'];
        
        // Novo upload
        if (!empty($_FILES['imagem']['name'])) {
            $nova_imagem = uploadImagem($_FILES['imagem']);
            if ($nova_imagem) {
                $imagem = $nova_imagem;
                // Remover imagem antiga se existir
                if ($noticia['imagem'] && file_exists($noticia['imagem'])) {
                    unlink($noticia['imagem']);
                }
            }
        }
        
        $stmt = $pdo->prepare("UPDATE noticias SET titulo = ?, noticia = ?, imagem = ? WHERE id = ?");
        $stmt->execute([$titulo, $conteudo, $imagem, $id]);
        
        mensagem('success', 'Notícia atualizada com sucesso!');
        redirecionar('noticia.php?id=' . $id);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Notícia - MundinhoNews</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="container">
        <div class="form-container" style="max-width: 700px;">
            <h2 class="text-center mb-4" style="color: var(--azul-primario);">✏️ Editar Notícia</h2>
            <?php exibirMensagem(); ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="titulo">Título *</label>
                    <input type="text" id="titulo" name="titulo" required maxlength="200" 
                           value="<?= limpar($noticia['titulo']) ?>">
                </div>
                
                <?php if ($noticia['imagem']): ?>
                <div class="form-group">
                    <label>Imagem atual:</label><br>
                    <img src="<?= limpar($noticia['imagem']) ?>" alt="Atual" style="max-width: 200px; border-radius: 8px;">
                </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="imagem">Nova imagem (opcional)</label>
                    <input type="file" id="imagem" name="imagem" accept="image/*">
                </div>
                
                <div class="form-group">
                    <label for="noticia">Conteúdo *</label>
                    <textarea id="noticia" name="noticia" rows="10" required><?= limpar($noticia['noticia']) ?></textarea>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn flex-grow-1">💾 Salvar Alterações</button>
                    <a href="noticia.php?id=<?= $noticia['id'] ?>" class="btn btn-outline">Cancelar</a>
                </div>
            </form>
            
            <div class="mt-4">
                <a href="excluir_noticia.php?id=<?= $noticia['id'] ?>" 
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('Tem certeza que deseja EXCLUIR permanentemente esta notícia?')">
                    🗑️ Excluir Notícia
                </a>
            </div>
        </div>
    </main>
    
    <?php include 'footer.php'; ?>
</body>
</html>