<?php
// usuarios.php - Apenas para administradores
session_start();
require_once 'conexao.php';
require_once 'funcoes.php';
require_once 'verifica_login.php';

exigirAdmin();

$pdo = conectar();
$stmt = $pdo->query("SELECT id, nome, email, tipo, ativo, data_cadastro FROM usuarios ORDER BY nome");
$usuarios = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - MundinhoNews</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="container">
        <?php exibirMensagem(); ?>
        
        <div class="dashboard-header">
            <h1>👥 Gerenciar Usuários</h1>
            <a href="cadastro.php" class="btn">+ Novo Usuário</a>
        </div>
        
        <div class="tabela-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Tipo</th>
                        <th>Status</th>
                        <th>Cadastro</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $user): ?>
                        <tr>
                            <td><?= limpar($user['nome']) ?></td>
                            <td><?= limpar($user['email']) ?></td>
                            <td>
                                <span class="badge bg-<?= $user['tipo'] === 'admin' ? 'primary' : 'info' ?>">
                                    <?= ucfirst($user['tipo']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $user['ativo'] ? 'success' : 'secondary' ?>">
                                    <?= $user['ativo'] ? 'Ativo' : 'Inativo' ?>
                                </span>
                            </td>
                            <td><?= formatarData($user['data_cadastro']) ?></td>
                            <td>
                                <a href="editar_usuario.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline">✏️</a>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <a href="excluir_usuario.php?id=<?= $user['id'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Excluir este usuário?')">🗑️</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <p class="mt-3">
            <a href="dashboard.php" class="btn btn-outline">← Voltar ao Painel</a>
        </p>
    </main>
    
    <?php include 'footer.php'; ?>
</body>
</html>