<?php
// index.php - MundinhoNews
require_once 'funcoes.php';

try {
    $pdo = conectar();
    
    // Buscar notícias com total de likes
    $stmt = $pdo->prepare("
        SELECT n.*, u.nome as autor_nome, u.avatar as autor_avatar,
               (SELECT COUNT(*) FROM likes WHERE noticia_id = n.id) as total_likes
        FROM noticias n 
        INNER JOIN usuarios u ON n.autor = u.id 
        WHERE n.status = 'publicada'
        ORDER BY n.data DESC 
        LIMIT 20
    ");
    $stmt->execute();
    $noticias = $stmt->fetchAll();
    
    // Estatísticas
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM noticias WHERE status = 'publicada'");
    $total_noticias = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
    $total_usuarios = $stmt->fetch()['total'];
} catch (Exception $e) {
    $noticias = [];
    $total_noticias = 0;
    $total_usuarios = 0;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MundinhoNews - Seu Portal de Notícias</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <!-- HERO SECTION -->
    <section class="home-hero">
        <div class="hero-content fade-in">
            <h1>🗞️ MundinhoNews</h1>
            <p>Onde as notícias ganham vida! Junte-se a nossa comunidade de jornalistas cidadãos e compartilhe histórias que importam.</p>
            
            <div class="hero-stats">
                <div class="hero-stat">
                    <h3><?= $total_noticias ?></h3>
                    <p>Notícias Publicadas</p>
                </div>
                <div class="hero-stat">
                    <h3><?= $total_usuarios ?></h3>
                    <p>Jornalistas Ativos</p>
                </div>
            </div>
            
            <div class="social-links">
                <a href="#" class="social-link" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-link" title="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-link" title="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-link" title="YouTube"><i class="fab fa-youtube"></i></a>
            </div>
            
            <div class="mt-4">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="nova_noticia.php" class="btn btn-lg">
                        <i class="fas fa-pen-fancy"></i> Criar Nova Notícia
                    </a>
                <?php else: ?>
                    <a href="cadastro.php" class="btn btn-lg">
                        <i class="fas fa-user-plus"></i> Comece Agora
                    </a>
                    <a href="login.php" class="btn btn-lg btn-outline" style="margin-left: 1rem; border-color: white; color: white;">
                        <i class="fas fa-sign-in-alt"></i> Entrar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <!-- CONTEÚDO PRINCIPAL -->
    <main class="container">
        <?php exibirMensagem(); ?>
        
        <div class="row">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h2 style="font-family: 'Playfair Display', serif; font-weight: 800; color: var(--azul-primario); font-size: 2.5rem;">
                        📰 Últimas Notícias
                    </h2>
                </div>
                
                <?php if (empty($noticias)): ?>
                    <div class="alert alert-info fade-in">
                        <i class="fas fa-info-circle"></i>
                        Nenhuma notícia publicada ainda. Seja o primeiro a compartilhar!
                    </div>
                <?php else: ?>
                    <?php foreach ($noticias as $noticia): ?>
                        <?php
                        // Verificar se usuário já curtiu esta notícia
                        $curtiu = false;
                        if (isset($_SESSION['user_id'])) {
                            $stmt = $pdo->prepare("SELECT id FROM likes WHERE noticia_id = ? AND usuario_id = ?");
                            $stmt->execute([$noticia['id'], $_SESSION['user_id']]);
                            $curtiu = $stmt->fetch() ? true : false;
                        }
                        ?>
                        <article class="noticia-card fade-in" onclick="abrirModal(<?= $noticia['id'] ?>)" style="cursor: pointer;">
                            <?php if ($noticia['imagem'] && file_exists($noticia['imagem'])): ?>
                                <img src="<?= limpar($noticia['imagem']) ?>" alt="<?= limpar($noticia['titulo']) ?>" class="noticia-card-img">
                            <?php else: ?>
                                <img src="https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800" alt="Notícia" class="noticia-card-img">
                            <?php endif; ?>
                            
                            <div class="noticia-card-body">
                                <h2 class="noticia-titulo">
                                    <?= limpar($noticia['titulo']) ?>
                                </h2>
                                
                                <div class="noticia-meta">
                                    <span>
                                        <img src="<?= $noticia['autor_avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=Default' ?>" 
                                             alt="Avatar" 
                                             style="width: 30px; height: 30px; border-radius: 50%; vertical-align: middle; margin-right: 8px;">
                                        <strong><?= limpar($noticia['autor_nome']) ?></strong>
                                    </span>
                                    <span><i class="far fa-clock"></i> <?= formatarData($noticia['data']) ?></span>
                                </div>
                                
                                <p class="noticia-resumo"><?= resumir($noticia['noticia'], 300) ?></p>
                                
                                <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
                                    <button class="like-btn <?= $curtiu ? 'liked' : '' ?>" 
                                            onclick="event.stopPropagation(); curtirNoticia(<?= $noticia['id'] ?>, this)">
                                        <i class="<?= $curtiu ? 'fas' : 'far' ?> fa-heart"></i>
                                        <span class="like-count"><?= $noticia['total_likes'] ?></span> curtidas
                                    </button>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- BARRA LATERAL -->
            <aside class="col-lg-4">
                <div class="sidebar-card fade-in">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="profile-avatar">
                            <img src="<?= $_SESSION['user_avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . $_SESSION['user_id'] ?>" alt="Avatar">
                        </div>
                        <h3 class="profile-name"><?= limpar($_SESSION['user_nome']) ?></h3>
                        <p class="profile-email"><?= limpar($_SESSION['user_email']) ?></p>
                        <a href="dashboard.php" class="btn btn-outline w-100 mb-2">
                            <i class="fas fa-tachometer-alt"></i> Meu Painel
                        </a>
                        <a href="perfil.php" class="btn btn-outline w-100 mb-2">
                            <i class="fas fa-user-cog"></i> Editar Perfil
                        </a>
                        <a href="nova_noticia.php" class="btn w-100 mb-2">
                            <i class="fas fa-plus"></i> Nova Notícia
                        </a>
                        <a href="logout.php" class="btn btn-danger w-100">
                            <i class="fas fa-sign-out-alt"></i> Sair
                        </a>
                    <?php else: ?>
                        <i class="fas fa-user-circle fa-5x" style="color: var(--azul-primario); margin-bottom: 1rem;"></i>
                        <h3 class="profile-name">Bem-vindo!</h3>
                        <p class="profile-email">Crie sua conta e comece a publicar</p>
                        <a href="cadastro.php" class="btn w-100 mb-2">
                            <i class="fas fa-user-plus"></i> Criar Conta Grátis
                        </a>
                        <a href="login.php" class="btn btn-outline w-100">
                            <i class="fas fa-sign-in-alt"></i> Já tenho conta
                        </a>
                    <?php endif; ?>
                </div>
                
                <div class="sidebar-card fade-in">
                    <h3 style="color: var(--azul-primario); margin-bottom: 1rem; font-family: 'Playfair Display', serif;">📌 Sobre o MundinhoNews</h3>
                    <p style="font-size: 0.95rem; line-height: 1.7; color: var(--texto-secundario);">
                        Fundado em 2026, o MundinhoNews é uma plataforma colaborativa onde qualquer pessoa pode se tornar um jornalista cidadão. 
                    </p>
                    <a href="quem_somos.php" class="btn btn-outline btn-sm w-100">
                        Saiba Mais <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="sidebar-card fade-in">
                    <h3 style="color: var(--azul-primario); margin-bottom: 1rem; font-family: 'Playfair Display', serif;">📱 Siga-nos</h3>
                    <div class="social-links" style="margin: 0;">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </aside>
        </div>
    </main>
    
    <!-- MODAL DE NOTÍCIA PROFISSIONAL -->
    <div class="modal-noticia" id="modalNoticia">
        <div class="modal-content">
            <button class="modal-close" onclick="fecharModal()">
                <i class="fas fa-times"></i>
            </button>
            <div class="modal-header">
                <span class="modal-badge">📰 Notícia Completa</span>
                <img src="" alt="" class="modal-img" id="modalImg">
            </div>
            <div class="modal-body">
                <h1 id="modalTitulo"></h1>
                
                <div class="modal-meta">
                    <div class="modal-meta-item">
                        <i class="far fa-calendar-alt"></i>
                        <span id="modalData"></span>
                    </div>
                    <div class="modal-meta-item">
                        <i class="far fa-clock"></i>
                        <span>Tempo de leitura: 3 min</span>
                    </div>
                </div>
                
                <div class="modal-autor">
                    <img src="" alt="" id="modalAvatar">
                    <div class="modal-autor-info">
                        <strong id="modalAutor"></strong>
                        <small>Jornalista Cidadão</small>
                    </div>
                </div>
                
                <div class="modal-conteudo" id="modalConteudo"></div>
                
                <div class="modal-actions">
                    <div class="modal-like-section">
                        <button class="like-btn" id="modalLikeBtn" onclick="curtirNoticiaModal()">
                            <i class="far fa-heart"></i>
                            <span id="modalLikeCount">0</span> curtidas
                        </button>
                    </div>
                    <a href="#" class="btn btn-outline" id="modalCompartilhar">
                        <i class="fas fa-share-alt"></i> Compartilhar
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   // No final do index.php, substitua TODO o script por este:

<script>
    // Dados das notícias para o modal
    const noticiasData = {
        <?php foreach ($noticias as $n): ?>
        <?= $n['id'] ?>: {
            titulo: `<?= addslashes($n['titulo']) ?>`,
            conteudo: `<?= addslashes($n['noticia']) ?>`,
            imagem: `<?= $n['imagem'] && file_exists($n['imagem']) ? $n['imagem'] : 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800' ?>`,
            autor: `<?= addslashes($n['autor_nome']) ?>`,
            avatar: `<?= $n['autor_avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=Default' ?>`,
            data: `<?= date('d/m/Y H:i', strtotime($n['data'])) ?>`,
            likes: <?= $n['total_likes'] ?>
        },
        <?php endforeach; ?>
    };
    
    let noticiaAtual = null;
    
    // Abrir modal
    function abrirModal(id) {
        const noticia = noticiasData[id];
        if (!noticia) return;
        
        noticiaAtual = id;
        
        document.getElementById('modalImg').src = noticia.imagem;
        document.getElementById('modalTitulo').textContent = noticia.titulo;
        document.getElementById('modalConteudo').innerHTML = noticia.conteudo.replace(/\n/g, '<br>');
        document.getElementById('modalAutor').textContent = noticia.autor;
        document.getElementById('modalAvatar').src = noticia.avatar;
        document.getElementById('modalData').textContent = 'Publicado em ' + noticia.data;
        document.getElementById('modalLikeCount').textContent = noticia.likes;
        
        // Resetar botão de like do modal
        const likeBtn = document.getElementById('modalLikeBtn');
        likeBtn.classList.remove('liked');
        likeBtn.querySelector('i').classList.remove('fas');
        likeBtn.querySelector('i').classList.add('far');
        
        document.getElementById('modalNoticia').classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    // Fechar modal
    function fecharModal() {
        document.getElementById('modalNoticia').classList.remove('active');
        document.body.style.overflow = 'auto';
        noticiaAtual = null;
    }
    
    // Fechar ao clicar fora
    document.getElementById('modalNoticia').addEventListener('click', function(e) {
        if (e.target === this) fecharModal();
    });
    
    // Fechar com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') fecharModal();
    });
    
    // Curtir notícia - FUNÇÃO CORRIGIDA
    function curtirNoticia(id, btn) {
        fetch('api_like.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                noticia_id: id
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na resposta da rede');
            }
            return response.json();
        })
        .then(data => {
            console.log('Resposta:', data);
            
            if (data.sucesso) {
                const icon = btn.querySelector('i');
                const count = btn.querySelector('.like-count');
                
                if (btn.classList.contains('liked')) {
                    btn.classList.remove('liked');
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                } else {
                    btn.classList.add('liked');
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                }
                
                count.textContent = data.total;
                
                // Atualizar modal se estiver aberto
                if (noticiaAtual === id) {
                    document.getElementById('modalLikeCount').textContent = data.total;
                    const modalBtn = document.getElementById('modalLikeBtn');
                    if (btn.classList.contains('liked')) {
                        modalBtn.classList.add('liked');
                        modalBtn.querySelector('i').classList.remove('far');
                        modalBtn.querySelector('i').classList.add('fas');
                    } else {
                        modalBtn.classList.remove('liked');
                        modalBtn.querySelector('i').classList.remove('fas');
                        modalBtn.querySelector('i').classList.add('far');
                    }
                }
            } else {
                alert('⚠️ ' + (data.erro || 'Erro ao curtir'));
                if (data.erro && data.erro.includes('login')) {
                    window.location.href = 'login.php';
                }
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('❌ Erro ao curtir. Verifique se está logado!');
        });
    }
    
    // Curtir pelo modal
    function curtirNoticiaModal() {
        if (noticiaAtual) {
            const btn = document.getElementById('modalLikeBtn');
            curtirNoticia(noticiaAtual, btn);
        }
    }
    
    // Compartilhar
    function compartilharNoticia() {
        if (navigator.share) {
            navigator.share({
                title: document.getElementById('modalTitulo').textContent,
                text: 'Confira esta notícia no MundinhoNews!',
                url: window.location.href
            });
        } else {
            alert('✅ Link copiado!');
        }
    }
</script>
</body>
</html>