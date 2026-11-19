<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login_admin.php');
    exit;
}

require 'includes/conexao.php';

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $preco = !empty($_POST['preco']) ? str_replace(',', '.', $_POST['preco']) : null;
    $categoria = trim($_POST['categoria'] ?? '');
    $cores = trim($_POST['cores'] ?? '');
    $opcoes = trim($_POST['opcoes'] ?? '');

    // Processamento do upload da imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $extensao = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($extensao, $permitidas)) {
            $nome_arquivo = uniqid('prod_') . '.' . $extensao;
            $destino = '../img/' . $nome_arquivo;
            $caminho_banco = 'img/' . $nome_arquivo;

            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
                $sql = "INSERT INTO produtos (nome, imagem, preco, cores, opcoes, categoria, ativo) VALUES (?, ?, ?, ?, ?, ?, 1)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nome, $caminho_banco, $preco, $cores, $opcoes, $categoria]);

                header('Location: index_admin.php');
                exit;
            } else {
                $erro = "Erro ao mover o arquivo de imagem para a pasta img/.";
            }
        } else {
            $erro = "Formato de imagem inválido. Use JPG, JPEG, PNG ou WEBP.";
        }
    } else {
        $erro = "A imagem do produto é obrigatória.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Produto - Admin</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap" rel="stylesheet">
</head>
<body style="background-color: #fffaf0; font-family: 'Fredoka', sans-serif; padding: 20px;">
    <div style="max-width: 500px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <h2>Adicionar Novo Produto ♡</h2>
        <?php if (!empty($erro)): ?><p style="color: #d9534f;"><?= $erro ?></p><?php endif; ?>
        
        <form action="cadastrar_produto.php" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 15px;">
            <div>
                <label>Nome do Produto:</label><br>
                <input type="text" name="nome" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
            </div>
            <div>
                <label>Preço (ex: 20.00):</label><br>
                <input type="text" name="preco" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
            </div>
            <div>
                <label>Categoria (ex: Chaveiro, Boneco):</label><br>
                <input type="text" name="categoria" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
            </div>
            <div>
                <label>Cores (opcional):</label><br>
                <input type="text" name="cores" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
            </div>
            <div>
                <label>Opções (opcional):</label><br>
                <input type="text" name="opcoes" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
            </div>
            <div>
                <label>Foto do Produto:</label><br>
                <input type="file" name="imagem" required accept="image/*">
            </div>
            <button type="submit" style="background: #ffb6c1; color: white; border: none; padding: 10px; border-radius: 6px; font-weight: bold; cursor: pointer;">Cadastrar Produto</button>
            <a href="index_admin.php" style="text-align: center; color: #666; text-decoration: none;">Voltar</a>
        </form>
    </div>
</body>
</html>