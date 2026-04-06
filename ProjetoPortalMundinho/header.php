<?php
// header.php
if (session_status() === PHP_SESSION_NONE) session_start();

$pagina_atual = basename($_SERVER['PHP_SELF']);
?>
<header>
    <nav class="navbar">
        <a href="index.php" class="logo">
            <span class="logo-icon">🗞️</span>
            Mundinho<span>News</span>
        </a>
        
        <ul class="nav-links">
            <li>
                <a href="index.php" class="<?= $pagina_atual === 'index.php' ? 'ativo' : '' ?>">
                    🏠 Início
                </a>
            </li>
            <li>
                <a href="quem_somos.php" class="<?= $pagina_atual === 'quem_somos.php' ? 'ativo' : '' ?>">
                    👥 Quem Somos
                </a>
            </li>
            <li>
                <a href="contato.php" class="<?= $pagina_atual === 'contato.php' ? 'ativo' : '' ?>">
                    📞 Contato
                </a>
            </li>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <li>
                    <a href="dashboard.php" class="<?= $pagina_atual === 'dashboard.php' ? 'ativo' : '' ?>">
                        📊 Painel
                    </a>
                </li>
                <li>
                    <a href="perfil.php">
                        👤 Perfil
                    </a>
                </li>
                <li>
                    <a href="logout.php" style="color: var(--perigo, #ef4444);">
                        🚪 Sair
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <a href="login.php" class="<?= $pagina_atual === 'login.php' ? 'ativo' : '' ?>">
                        🔐 Entrar
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<!-- Botão Theme Toggle -->
<button class="theme-toggle" onclick="toggleTheme()" title="Alternar Tema">
    <i class="fas fa-moon" id="theme-icon"></i>
</button>

<script>
// Theme Toggle
function toggleTheme() {
    const html = document.documentElement;
    const icon = document.getElementById('theme-icon');
    const currentTheme = html.getAttribute('data-theme');
    
    if (currentTheme === 'dark') {
        html.setAttribute('data-theme', 'light');
        icon.classList.remove('fa-sun');
        icon.classList.add('fa-moon');
        localStorage.setItem('theme', 'light');
    } else {
        html.setAttribute('data-theme', 'dark');
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
        localStorage.setItem('theme', 'dark');
    }
}

// Carregar tema salvo
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    const icon = document.getElementById('theme-icon');
    
    document.documentElement.setAttribute('data-theme', savedTheme);
    
    if (savedTheme === 'dark') {
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
    }
});
</script>