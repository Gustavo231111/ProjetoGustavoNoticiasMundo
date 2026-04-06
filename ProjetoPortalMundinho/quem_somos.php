<?php require_once 'funcoes.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quem Somos - MundinhoNews</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <section class="about-hero">
        <h1>🗞️ Sobre o MundinhoNews</h1>
        <p class="lead">Conheça nossa história e missão</p>
    </section>
    
    <main class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="fade-in">
                    <p class="lead text-center" style="max-width: 800px; margin: 0 auto 3rem; color: var(--texto-secundario);">
                        Mais que um portal de notícias, somos um movimento de democratização da informação.
                    </p>
                    
                    <div class="row align-items-center mb-5">
                        <div class="col-md-6">
                            <img src="https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=600" 
                                 alt="Redação" 
                                 class="img-fluid rounded-4 shadow"
                                 style="border: 4px solid var(--azul-suave);">
                        </div>
                        <div class="col-md-6">
                            <h2 style="color: var(--azul-primario); font-family: 'Playfair Display', serif;">📌 Nossa História</h2>
                            <p>
                                Fundado em <strong>2026</strong>, o MundinhoNews nasceu de uma ideia simples: 
                                <strong>toda pessoa tem uma história para contar</strong>.
                            </p>
                            <p>
                                Acreditamos que o jornalismo tradicional muitas vezes deixa de lado vozes importantes. 
                                Por isso, criamos uma plataforma onde <strong>você</strong> é o jornalista.
                            </p>
                            <p>
                                Em poucos meses, já somos <strong>centenas de jornalistas cidadãos</strong> 
                                compartilhando notícias que realmente importam para nossas comunidades.
                            </p>
                        </div>
                    </div>
                    
                    <h2 class="text-center mb-4" style="color: var(--azul-primario); font-family: 'Playfair Display', serif;">🎯 Nossos Pilares</h2>
                    
                    <div class="about-grid">
                        <div class="about-card">
                            <i class="fas fa-bullhorn"></i>
                            <h3>Voz Ativa</h3>
                            <p>Qualquer pessoa pode publicar. Não há barreiras, apenas histórias.</p>
                        </div>
                        <div class="about-card">
                            <i class="fas fa-check-double"></i>
                            <h3>Veracidade</h3>
                            <p>Compromisso com a verdade. Cada autor é responsável por seu conteúdo.</p>
                        </div>
                        <div class="about-card">
                            <i class="fas fa-users"></i>
                            <h3>Comunidade</h3>
                            <p>Juntos somos mais fortes. Uma rede de jornalistas cidadãos conectados.</p>
                        </div>
                        <div class="about-card">
                            <i class="fas fa-globe"></i>
                            <h3>Alcance</h3>
                            <p>Do local ao global. Sua notícia pode alcançar quem você nunca imaginou.</p>
                        </div>
                    </div>
                    
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                    </div>
                    
                    <div class="text-center mt-5">
                        <a href="cadastro.php" class="btn btn-lg">
                            <i class="fas fa-user-plus"></i> Faça Parte Dessa História
                        </a>
                        <a href="index.php" class="btn btn-outline btn-lg" style="margin-left: 1rem;">
                            <i class="fas fa-home"></i> Voltar ao Início
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>