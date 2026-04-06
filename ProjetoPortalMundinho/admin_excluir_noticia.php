<?php
// admin_excluir_noticia.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=portal_mundinho;charset=utf8mb4", "root", "");
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM noticias WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: admin_noticias.php?msg=Notícia excluída!&tipo=success");
    } catch (Exception $e) {
        header("Location: admin_noticias.php?msg=Erro ao excluir!&tipo=danger");
    }
} else {
    header("Location: admin_noticias.php");
}
exit;
?>