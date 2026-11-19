<?php
session_start();
// Protege a página: se não estiver logado como admin, redireciona para o login
// if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
//     header('Location: login_admin_certo.php');
//     exit;
// }

// Sobe um nível para acessar a conexão na pasta anterior
require 'includes/conexao.php';

// Busca todos os produtos do banco de dados ordenados por ID
$produtos = $pdo->query('SELECT * FROM produtos ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Pontos e Pelúcias</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap" rel="stylesheet">
</head>
<body style="background-color: #fffaf0; font-family: 'Fredoka', sans-serif; padding: 20px;">
    <div style="max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Painel Administrativo ♡</h2>
            <div>
                <a href="cadastrar_produto.php" style="background: #ffb6c1; color: #fff; padding: 10px 15px; border-radius: 8px; text-decoration: none; font-weight: bold; margin-right: 10px;">+ Novo Produto</a>
                <a href="index.php" style="color: #666; text-decoration: none;">Ver Site</a>
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid #eee; margin-bottom: 20px;">

        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #eee; color: #555;">
                    <th style="padding: 10px;">Imagem</th>
                    <th style="padding: 10px;">Nome</th>
                    <th style="padding: 10px;">Preço</th>
                    <th style="padding: 10px;">Categoria</th>
                    <th style="padding: 10px; text-align: center;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $p): ?>
                <tr style="border-bottom: 1px solid #f9f9f9;">
                    <td style="padding: 10px;"><img src="../<?= e($p['imagem']) ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;"></td>
                    <td style="padding: 10px; font-weight: 500;"><?= e($p['nome']) ?></td>
                    <td style="padding: 10px;"><?= $p['preco'] !== null ? 'R$ ' . number_format($p['preco'], 2, ',', '.') : 'Sob consulta' ?></td>
                    <td style="padding: 10px;"><?= e($p['categoria']) ?></td>
                    <td style="padding: 10px; text-align: center;">
                        <a href="editar_produto_certo.php?id=<?= $p['id'] ?>" style="color: #2b7a78; text-decoration: none; margin-right: 10px; font-weight: bold;">Editar</a>
                        <a href="excluir_produto.php?id=<?= $p['id'] ?>" style="color: #d9534f; text-decoration: none; font-weight: bold;" onclick="return confirm('Tem certeza que deseja excluir este produto?')">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</body>
</html>