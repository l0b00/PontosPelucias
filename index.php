<?php
require 'includes/conexao.php';

$categorias = $pdo->query('SELECT nome, imagem FROM categorias ORDER BY ordem, id')->fetchAll();

require 'includes/cabecalho.php';
?>

    <aside>
        <ul class="info">
            <li class="text_info"><h3>100% artesanal</h3><p class="sub_info">produtos feitos manualmente</p></li>
            <li class="text_info"><h3>Pagamento</h3><p class="sub_info">somente pix</p></li>
            <li class="text_info"><h3>Entrega pelos correios</h3><p class="sub_info">do atelie direto para sua casa</p></li>
        </ul>
    </aside>

    <div class="container">
    <?php foreach ($categorias as $categoria): ?>
    <div class="cards">
        <!-- O link abre e fecha DENTRO do card, englobando certinho a imagem e o texto -->
        <a class="inf_txt" href="catalogo.php?categoria=<?= urlencode($categoria['nome']) ?>" class="categoria">

        <a href="catalogo.php?categoria=<?= urlencode($categoria['nome']) ?>" class="categoria">

            <img class="img" src="<?= e($categoria['imagem']) ?>" alt="<?= e($categoria['nome']) ?>">
            <p class="cat_txt"><?= e($categoria['nome']) ?></p>
        </a>
    </div>
    <?php endforeach; ?>
</div>

    <div class="container_colecao">
        <div class="colecao"><h2 class="titulo_colecao">Coleções</h2><p class="colecao_txt">Kit e conjuntos dos amigurumis mais amados em um só lugar</p></div>
        <a class="box_colecao"><img class="img_colecao" src="img/colecao2.jpeg"></a>
    </div>

 <?php include 'rodape.php'; ?>
 
</body>
</html>
