<?php
require_once 'funcoes.php';

$mensagem_enviada = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = limpar($_POST['nome'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $assunto = limpar($_POST['assunto'] ?? '');
    $mensagem = limpar($_POST['mensagem'] ?? '');
    
    if ($nome && $email && $assunto && $mensagem) {
        $mensagem_enviada = true;
        mensagem('success', '✅ Mensagem enviada com sucesso! Respondemos em até 24 horas.');
    } else {
        mensagem('danger', '⚠️ Preencha todos os campos obrigatórios.');
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contato - MundinhoNews</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <section class="contact-hero">
        <h1>📞 Fale Conosco</h1>
        <p class="lead">Estamos aqui para ouvir você. Envie sua mensagem!</p>
    </section>
    
    <main class="container">
        <?php exibirMensagem(); ?>
        
        <?php if ($mensagem_enviada): ?>
            <div class="text-center py-5 fade-in">
                <i class="fas fa-check-circle fa-5x text-success mb-4"></i>
                <h2 style="font-family: 'Playfair Display', serif; color: var(--azul-primario);">Mensagem Enviada!</h2>
                <p class="lead">Obrigado pelo contato. Nossa equipe responderá em breve.</p>
                <a href="contato.php" class="btn btn-outline mt-3">
                    <i class="fas fa-envelope"></i> Enviar Nova Mensagem
                </a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-5 mb-4">
                    <div class="contact-card fade-in">
                        <h3 style="font-family: 'Playfair Display', serif; color: var(--azul-primario); margin-bottom: 1.5rem;">
                            📍 Informações de Contato
                        </h3>
                        
                        <div class="contact-info-item" style="display: flex; align-items: center; gap: 1rem; padding: 1.5rem; background: var(--azul-suave); border-radius: 16px; margin-bottom: 1rem; transition: all 0.3s;">
                            <i class="fas fa-envelope" style="font-size: 2rem; color: var(--azul-primario); width: 50px; text-align: center;"></i>
                            <div>
                                <strong>Email</strong>
                                <p class="mb-0" style="color: var(--texto-secundario);">contato@mundinho.com</p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item" style="display: flex; align-items: center; gap: 1rem; padding: 1.5rem; background: var(--azul-suave); border-radius: 16px; margin-bottom: 1rem; transition: all 0.3s;">
                            <i class="fas fa-phone" style="font-size: 2rem; color: var(--azul-primario); width: 50px; text-align: center;"></i>
                            <div>
                                <strong>Telefone</strong>
                                <p class="mb-0" style="color: var(--texto-secundario);">(11) 99999-9999</p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item" style="display: flex; align-items: center; gap: 1rem; padding: 1.5rem; background: var(--azul-suave); border-radius: 16px; margin-bottom: 1rem; transition: all 0.3s;">
                            <i class="fas fa-map-marker-alt" style="font-size: 2rem; color: var(--azul-primario); width: 50px; text-align: center;"></i>
                            <div>
                                <strong>Localização</strong>
                                <p class="mb-0" style="color: var(--texto-secundario);">São Paulo - SP, Brasil</p>
                            </div>
                        </div>
                        
                        <div class="social-links" style="margin-top: 2rem;">
                            <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-7">
                    <div class="contact-card contact-form fade-in">
                        <h3 style="font-family: 'Playfair Display', serif; color: var(--azul-primario); margin-bottom: 1.5rem;">
                            ✉️ Envie uma Mensagem
                        </h3>
                        
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nome"><i class="fas fa-user me-2" style="color: var(--azul-primario);"></i>Nome *</label>
                                        <input type="text" id="nome" name="nome" required class="form-control" 
                                               placeholder="Seu nome completo"
                                               value="<?= isset($_POST['nome']) ? limpar($_POST['nome']) : '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email"><i class="fas fa-envelope me-2" style="color: var(--azul-primario);"></i>Email *</label>
                                        <input type="email" id="email" name="email" required class="form-control"
                                               placeholder="seu@email.com"
                                               value="<?= isset($_POST['email']) ? limpar($_POST['email']) : '' ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="assunto"><i class="fas fa-tag me-2" style="color: var(--azul-primario);"></i>Assunto *</label>
                                <input type="text" id="assunto" name="assunto" required class="form-control"
                                       placeholder="Sobre o que é sua mensagem?"
                                       value="<?= isset($_POST['assunto']) ? limpar($_POST['assunto']) : '' ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="mensagem"><i class="fas fa-comment-dots me-2" style="color: var(--azul-primario);"></i>Mensagem *</label>
                                <textarea id="mensagem" name="mensagem" rows="8" required class="form-control"
                                          placeholder="Escreva sua mensagem aqui..."><?= isset($_POST['mensagem']) ? limpar($_POST['mensagem']) : '' ?></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-lg w-100">
                                <i class="fas fa-paper-plane"></i> Enviar Mensagem
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>
    
    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>