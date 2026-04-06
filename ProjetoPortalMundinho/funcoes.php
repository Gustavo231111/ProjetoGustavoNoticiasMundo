<?php
// funcoes.php - TODAS AS FUNÇÕES CENTRALIZADAS

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function limpar($texto) {
    return htmlspecialchars(trim($texto), ENT_QUOTES, 'UTF-8');
}

function mensagem($tipo, $texto) {
    $_SESSION['msg_tipo'] = $tipo;
    $_SESSION['msg_texto'] = $texto;
}

function exibirMensagem() {
    if (isset($_SESSION['msg_texto'])) {
        $tipo = limpar($_SESSION['msg_tipo']);
        $texto = limpar($_SESSION['msg_texto']);
        $classe = "alert-{$tipo}";
        echo "<div class='alert {$classe} fade-in'>{$texto}</div>";
        unset($_SESSION['msg_tipo'], $_SESSION['msg_texto']);
    }
}

function hashSenha($senha) {
    return password_hash($senha, PASSWORD_DEFAULT);
}

function verificarSenha($senha, $hash) {
    return password_verify($senha, $hash);
}

function redirecionar($url) {
    header("Location: $url");
    exit;
}

function resumir($texto, $limite = 180) {
    $texto = strip_tags($texto);
    if (mb_strlen($texto) <= $limite) return $texto;
    return mb_substr($texto, 0, $limite) . '...';
}

function formatarData($data) {
    return date('d/m/Y H:i', strtotime($data));
}

function uploadImagem($arquivo) {
    if ($arquivo['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($ext, $permitidas)) {
            $nome = uniqid('noticia_') . '.' . $ext;
            $destino = 'uploads/' . $nome;
            
            if (!file_exists('uploads')) {
                mkdir('uploads', 0777, true);
            }
            
            if (move_uploaded_file($arquivo['tmp_name'], $destino)) {
                return $destino;
            }
        }
    }
    return null;
}

function exigirLogin() {
    if (!isset($_SESSION['user_id'])) {
        mensagem('warning', '⚠️ Faça login para acessar esta página.');
        redirecionar('login.php');
    }
}

function isAdmin() {
    return isset($_SESSION['user_tipo']) && $_SESSION['user_tipo'] === 'admin';
}

function conectar() {
    try {
        $pdo = new PDO(
            "mysql:host=localhost;dbname=portal_mundinho;charset=utf8mb4",
            "root", 
            "",
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die("Erro de conexão: " . $e->getMessage());
    }
}
?>