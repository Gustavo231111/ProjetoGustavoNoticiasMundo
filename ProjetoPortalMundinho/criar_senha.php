<?php
// criar_senha.php - RODE ESTE ARQUIVO PRIMEIRO!
// Acesse: http://localhost/ProjetoPortalMundinho/criar_senha.php

echo "<h1>🔐 Gerador de Senhas - MundinhoNews</h1>";
echo "<p>Execute este arquivo UMA VEZ para criar as senhas corretas no banco!</p><hr>";

// Conexão
$pdo = new PDO("mysql:host=localhost;dbname=portal_mundinho;charset=utf8mb4", "root", "");

// Senha admin123
$senha_admin = password_hash("admin123", PASSWORD_DEFAULT);
echo "<h3>Senha Admin (admin123):</h3>";
echo "<code>$senha_admin</code><br><br>";

// Senha 123456
$senha_user = password_hash("123456", PASSWORD_DEFAULT);
echo "<h3>Senha Usuário (123456):</h3>";
echo "<code>$senha_user</code><br><br>";

// Atualizar no banco
try {
    // Atualizar admin
    $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE email = 'admin@mundinho.com'");
    $stmt->execute([$senha_admin]);
    echo "<div style='background: #d1fae5; padding: 1rem; border-radius: 10px; color: #065f46;'>
            ✅ <strong>Senha do ADMIN atualizada!</strong><br>
            Email: admin@mundinho.com<br>
            Senha: admin123
          </div>";
    
    // Atualizar usuário teste
    $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE email = 'teste@mundinho.com'");
    $stmt->execute([$senha_user]);
    echo "<div style='background: #d1fae5; padding: 1rem; border-radius: 10px; color: #065f46; margin-top: 1rem;'>
            ✅ <strong>Senha do USUÁRIO atualizada!</strong><br>
            Email: teste@mundinho.com<br>
            Senha: 123456
          </div>";
    
    echo "<hr><h3>✅ AGORA VOCÊ PODE LOGAR!</h3>";
    echo "<a href='login.php' style='background: #0066cc; color: white; padding: 1rem 2rem; text-decoration: none; border-radius: 10px; display: inline-block; margin-top: 1rem;'>IR PARA LOGIN</a>";
    
} catch (Exception $e) {
    echo "<div style='background: #fee2e2; padding: 1rem; border-radius: 10px; color: #991b1b;'>
            ❌ Erro: " . $e->getMessage() . "
          </div>";
    echo "<p><strong>Provavelmente o banco não existe. Importe o dump.sql primeiro!</strong></p>";
}
?>