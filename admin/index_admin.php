<?php
require 'includes/conexao.php';

$categorias = $pdo->query('SELECT id, nome, imagem, ordem FROM categorias ORDER BY ordem, id')->fetchAll();

$totalCategorias = count($categorias);
$totalProdutos = $pdo->query('SELECT COUNT(*) FROM produtos')->fetchColumn() ?? 0;

require 'includes/cabecalho.php';
?>

    <div class="admin-header-bar">
        <h1>Painel Administrativo</h1>
        <p>Gerencie o conteúdo do seu ateliê</p>
    </div>

    <aside>
        <ul class="info">
            <li class="text_info">
                <h3><?= $totalCategorias ?></h3>
                <p class="sub_info">Categorias cadastradas</p>
            </li>
            <li class="text_info">
                <h3><?= $totalProdutos ?></h3>
                <p class="sub_info">Produtos no catálogo</p>
            </li>
            <li class="text_info">
                <h3>Admin</h3>
                <p class="sub_info">Sessão ativa com segurança</p>
            </li>
        </ul>
    </aside>

    <div class="container">
        <div class="admin-section-header" style="width: 100%; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Gerenciar Categorias</h2>
            <a href="categoria-nova.php" class="btn-admin-novo">+ Nova Categoria</a>
        </div>

        <?php foreach ($categorias as $categoria): ?>
        <div class="cards admin-card">
            <img class="img" src="<?= e($categoria['imagem']) ?>">
            <p class="cat_txt"><?= e($categoria['nome']) ?></p>
            <div class="admin-actions">
                <a href="categoria-editar.php?id=<?= $categoria['id'] ?>" class="btn-editar">Editar</a>
                <a href="categoria-excluir.php?id=<?= $categoria['id'] ?>" class="btn-excluir" onclick="return confirm('Deseja realmente excluir esta categoria?');">Excluir</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="container_colecao">
        <div class="colecao">
            <h2 class="titulo_colecao">Gerenciar Coleções em Destaque</h2>
            <p class="colecao_txt">Altere a imagem e os kits em evidência na página inicial</p>
        </div>
        <div style="display: flex; flex-direction: column; align-items: center; gap: 10px;">
            <a class="box_colecao"><img class="img_colecao" src="img/colecao2.jpeg"></a>
            <a href="colecao-editar.php" class="btn-admin-novo">Alterar Imagem da Coleção</a>
        </div>
    </div>
</body>
</html>