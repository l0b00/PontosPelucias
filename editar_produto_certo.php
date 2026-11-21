<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login_admin_certo.php');
    exit;
}

require 'includes/conexao.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM produtos WHERE id = ?');
$stmt->execute([$id]);
$produto = $stmt->fetch();

if (!$produto) {
    exit('Produto não encontrado.');
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $preco = !empty($_POST['preco']) ? str_replace(',', '.', $_POST['preco']) : null;
    $categoria = trim($_POST['categoria'] ?? '');
    $cores = trim($_POST['cores'] ?? '');
    $opcoes = trim($_POST['opcoes'] ?? '');
    $caminho_banco = $produto['imagem']; // Mantém a imagem antiga por padrão

    // Se uma nova imagem foi enviada, substitui (só aceita formatos de imagem)
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $extensao = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($extensao, $permitidas)) {
            $destino = '../img/' . uniqid('prod_') . '.' . $extensao;
            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
                $caminho_banco = 'img/' . basename($destino);
            }
        } else {
            $erro = "Formato de imagem inválido. Use JPG, JPEG, PNG ou WEBP.";
        }
    }

    if ($erro === '') {
        $sql = "UPDATE produtos SET nome = ?, imagem = ?, preco = ?, cores = ?, opcoes = ?, categoria = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $caminho_banco, $preco, $cores, $opcoes, $categoria, $id]);

        header('Location: index_admin.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Produto - Admin</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap" rel="stylesheet">
</head>
<body style="background-color: #fffaf0; font-family: 'Fredoka', sans-serif; padding: 20px;">
    <div style="max-width: 500px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <h2>Editar Produto ♡</h2>
        <?php if ($erro !== ''): ?>
            <p style="color: #d9534f; font-weight: 500;"><?= e($erro) ?></p>
        <?php endif; ?>
        <form action="editar_produto.php?id=<?= $produto['id'] ?>" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 15px;">
            <div>
                <label>Nome:</label><br>
                <input type="text" name="nome" value="<?= e($produto['nome']) ?>" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
            </div>
            <div>
                <label>Preço:</label><br>
                <input type="text" name="preco" value="<?= $produto['preco'] ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
            </div>
            <div>
                <label>Categoria:</label><br>
                <input type="text" name="categoria" value="<?= e($produto['categoria']) ?>" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
            </div>
            <div>
                <label>Cores:</label><br>
                <input type="text" name="cores" value="<?= e($produto['cores']) ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
            </div>
            <div>
                <label>Opções:</label><br>
                <input type="text" name="opcoes" value="<?= e($produto['opcoes']) ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
            </div>
            <div>
                <label>Alterar Imagem (opcional):</label><br>
                <input type="file" name="imagem" accept="image/*">
            </div>
            <button type="submit" style="background: #2b7a78; color: white; border: none; padding: 10px; border-radius: 6px; font-weight: bold; cursor: pointer;">Salvar Alterações</button>
            <a href="index_admin.php" style="text-align: center; color: #666; text-decoration: none;">Cancelar</a>
        </form>
    </div>
</body>
</html>