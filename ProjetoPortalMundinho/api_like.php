<?php
// api_like.php - Sistema de Curtidas
header('Content-Type: application/json');
session_start();

// Verificar se está logado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Faça login para curtir']);
    exit;
}

// Conexão direta
try {
    $pdo = new PDO("mysql:host=localhost;dbname=portal_mundinho;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro de conexão: ' . $e->getMessage()]);
    exit;
}

// Receber dados
$data = json_decode(file_get_contents('php://input'), true);
$noticia_id = isset($data['noticia_id']) ? (int)$data['noticia_id'] : 0;
$usuario_id = $_SESSION['user_id'];

if (!$noticia_id) {
    echo json_encode(['sucesso' => false, 'erro' => 'ID da notícia inválido']);
    exit;
}

try {
    // Verificar se já curtiu
    $stmt = $pdo->prepare("SELECT id FROM likes WHERE noticia_id = ? AND usuario_id = ?");
    $stmt->execute([$noticia_id, $usuario_id]);
    $like = $stmt->fetch();
    
    if ($like) {
        // Remover like (descurtir)
        $stmt = $pdo->prepare("DELETE FROM likes WHERE id = ?");
        $stmt->execute([$like['id']]);
        $acao = 'removido';
    } else {
        // Adicionar like
        $stmt = $pdo->prepare("INSERT INTO likes (noticia_id, usuario_id) VALUES (?, ?)");
        $stmt->execute([$noticia_id, $usuario_id]);
        $acao = 'adicionado';
    }
    
    // Contar total atualizado
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM likes WHERE noticia_id = ?");
    $stmt->execute([$noticia_id]);
    $total = $stmt->fetch()['total'];
    
    echo json_encode([
        'sucesso' => true,
        'acao' => $acao,
        'total' => $total
    ]);
    
} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}
?>