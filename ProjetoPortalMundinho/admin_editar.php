<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=portal_mundinho;charset=utf8mb4", "root", "");
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) header("Location: admin.php");

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $tipo = $_POST['tipo'];
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    $senha = $_POST['senha'];
    
    if (!empty($senha)) {
        $stmt = $pdo->prepare("UPDATE usuarios SET nome=?, email=?, tipo=?, ativo=?, senha=? WHERE id=?");
        $stmt->execute([$nome, $email, $tipo, $ativo, password_hash($senha, PASSWORD_DEFAULT), $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE usuarios SET nome=?, email=?, tipo=?, ativo=? WHERE id=?");
        $stmt->execute([$nome, $email, $tipo, $ativo, $id]);
    }
    header("Location: admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="card mt-5" style="max-width: 600px; margin: 3rem auto; border-radius: 15px;">
            <div class="card-body p-4">
                <h3 class="mb-4">✏️ Editar Usuário</h3>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo</label>
                        <select name="tipo" class="form-select">
                            <option value="usuario" <?= $usuario['tipo'] === 'usuario' ? 'selected' : '' ?>>Usuário</option>
                            <option value="admin" <?= $usuario['tipo'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nova Senha (opcional)</label>
                        <input type="password" name="senha" class="form-control" placeholder="Deixe em branco para manter">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="ativo" class="form-check-input" id="ativo" <?= $usuario['ativo'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="ativo">Ativo</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Salvar</button>
                    <a href="admin.php" class="btn btn-outline-secondary w-100 mt-2">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>