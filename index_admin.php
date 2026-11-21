<?php
session_start();
// Protege a página: se não estiver logado como admin, redireciona para o login
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login_admin_certo.php');
    exit;
}

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
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=DynaPuff:wght@400..700&family=Fredoka:wght@300..700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin_container">

        <div class="admin_topo">
            <h2 class="admin_titulo">Painel Administrativo ♡</h2>
            <div class="admin_acoes_topo">
                <a href="admin_cadastrar_produto.php" class="btn_novo_produto">+ Novo Produto</a>
                <a href="index.php" class="link">Ver Site</a>
            </div>
        </div>

        <hr class="admin_divisor">

        <div class="admin_tabela_wrap">
            <table class="admin_tabela">
                <thead>
                    <tr>
                        <th>Imagem</th>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Categoria</th>
                        <th class="admin_acoes_col">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produtos as $p): ?>
                    <tr>
                        <td><img src="../<?= e($p['imagem']) ?>" alt="<?= e($p['nome']) ?>" class="admin_img"></td>
                        <td class="admin_nome"><?= e($p['nome']) ?></td>
                        <td><?= $p['preco'] !== null ? 'R$ ' . number_format($p['preco'], 2, ',', '.') : 'Sob consulta' ?></td>
                        <td><?= e($p['categoria']) ?></td>
                        <td class="admin_acoes_col">
                            <a href="editar_produto_certo.php?id=<?= $p['id'] ?>" class="acao_editar">Editar</a>
                            <a href="excluir_produto_admin.php?id=<?= $p['id'] ?>" class="acao_excluir" onclick="return confirm('Tem certeza que deseja excluir este produto?')">Excluir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>