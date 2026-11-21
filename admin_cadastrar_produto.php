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
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=DynaPuff:wght@400..700&family=Fredoka:wght@300..700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin_form_container">
        <h2 class="admin_form_titulo">Adicionar Novo Produto ♡</h2>

        <?php if (!empty($erro)): ?>
            <p class="admin_form_erro"><?= $erro ?></p>
        <?php endif; ?>

        <form action="cadastrar_produto.php" method="POST" enctype="multipart/form-data" class="admin_form">
            <div class="admin_campo">
                <label for="nome">Nome do Produto:</label>
                <input type="text" id="nome" name="nome" class="admin_input" required>
            </div>

            <div class="admin_campo">
                <label for="preco">Preço (ex: 20.00):</label>
                <input type="text" id="preco" name="preco" class="admin_input">
            </div>

            <div class="admin_campo">
                <label for="categoria">Categoria (ex: Chaveiro, Boneco):</label>
                <input type="text" id="categoria" name="categoria" class="admin_input" required>
            </div>

            <div class="admin_campo">
                <label for="cores">Cores (opcional):</label>
                <input type="text" id="cores" name="cores" class="admin_input">
            </div>

            <div class="admin_campo">
                <label for="opcoes">Opções (opcional):</label>
                <input type="text" id="opcoes" name="opcoes" class="admin_input">
            </div>

            <div class="admin_campo">
                <label for="imagem">Foto do Produto:</label>
                <input type="file" id="imagem" name="imagem" class="admin_file" required accept="image/*">
            </div>

            <button type="submit" class="btn_admin_submit">Cadastrar Produto</button>
            <a href="index_admin.php" class="admin_voltar">Voltar</a>
        </form>
    </div>
</body>
</html>