<?php
// testar_likes.php - Teste o sistema de likes
session_start();

echo "<h1>🧪 Teste do Sistema de Likes</h1><hr>";

// Verificar sessão
echo "<h3>1. Sessão:</h3>";
if (isset($_SESSION['user_id'])) {
    echo "✅ Usuário logado: ID " . $_SESSION['user_id'] . " - " . $_SESSION['user_nome'];
} else {
    echo "❌ Usuário NÃO logado! <a href='login.php'>Faça login</a>";
}
echo "<br><br>";

// Verificar conexão
echo "<h3>2. Conexão com Banco:</h3>";
try {
    $pdo = new PDO("mysql:host=localhost;dbname=portal_mundinho;charset=utf8mb4", "root", "");
    echo "✅ Conexão OK!";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage();
}
echo "<br><br>";

// Verificar tabela likes
echo "<h3>3. Tabela likes:</h3>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM likes");
    $total = $stmt->fetch()['total'];
    echo "✅ Tabela existe! Total de likes: <strong>$total</strong>";
} catch (Exception $e) {
    echo "❌ Tabela NÃO existe! Execute este SQL:<br>";
    echo "<code>CREATE TABLE likes (id INT PRIMARY KEY AUTO_INCREMENT, noticia_id INT NOT NULL, usuario_id INT NOT NULL, data DATETIME DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (noticia_id) REFERENCES noticias(id) ON DELETE CASCADE, FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE, UNIQUE KEY unique_like (noticia_id, usuario_id));</code>";
}
echo "<br><br>";

// Verificar notícias
echo "<h3>4. Notícias no banco:</h3>";
try {
    $stmt = $pdo->query("SELECT id, titulo FROM noticias ORDER BY id DESC LIMIT 5");
    $noticias = $stmt->fetchAll();
    if (count($noticias) > 0) {
        echo "✅ Notícias encontradas:<br>";
        foreach ($noticias as $n) {
            echo "• ID {$n['id']}: {$n['titulo']}<br>";
        }
    } else {
        echo "❌ Nenhuma notícia! Crie uma notícia primeiro.";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage();
}
echo "<br><br>";

// Verificar api_like.php
echo "<h3>5. Arquivo api_like.php:</h3>";
if (file_exists('api_like.php')) {
    echo "✅ Arquivo existe!";
} else {
    echo "❌ Arquivo NÃO existe!";
}
echo "<br><br>";

echo "<hr><h3>✅ Se tudo estiver verde, os likes devem funcionar!</h3>";
echo "<a href='index.php' style='background: #0066cc; color: white; padding: 1rem 2rem; text-decoration: none; border-radius: 10px; display: inline-block; margin-top: 1rem;'>Voltar ao Site</a>";
?>