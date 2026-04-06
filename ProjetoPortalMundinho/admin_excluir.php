<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=portal_mundinho;charset=utf8mb4", "root", "");
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id && $id != $_SESSION['user_id']) {
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
}
header("Location: admin.php");
exit;
?>