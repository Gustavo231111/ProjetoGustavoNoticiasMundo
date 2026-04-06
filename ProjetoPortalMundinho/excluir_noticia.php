<?php
// excluir_noticia.php
session_start();
require_once 'conexao.php';
require_once 'funcoes.php';
require_once 'verifica_login.php';

exigirLogin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) redirecionar('dashboard.php');

$pdo = conectar();

// Verificar permissão
$stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
$stmt->execute([$id]);
$noticia = $stmt->fetch();

if (!$noticia) {
    mensagem('danger', 'Notícia não encontrada.');
    redirecionar('dashboard.php');
}

// Só autor ou admin pode excluir
if ($noticia['autor'] != $_SESSION['user_id'] && !isAdmin()) {
    mensagem('danger', 'Sem permissão para excluir esta notícia.');
    redirecionar('dashboard.php');
}

// Remover imagem se existir
if ($noticia['imagem'] && file_exists($noticia['imagem'])) {
    unlink($noticia['imagem']);
}

// Excluir do banco
$stmt = $pdo->prepare("DELETE FROM noticias WHERE id = ?");
$stmt->execute([$id]);

mensagem('success', 'Notícia excluída com sucesso!');
redirecionar('dashboard.php');
?>