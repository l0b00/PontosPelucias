<?php
require 'includes/conexao.php';

$produtos = $pdo->query('SELECT id, nome, imagem FROM produtos WHERE ativo = 1 ORDER BY id')->fetchAll();

require 'includes/cabecalho.php';
?>

    <?php // A quantidade de cards por linha se ajusta à largura da tela (ver .catalogo no style.css) ?>
    <div class="catalogo">
        <?php foreach ($produtos as $produto): ?>
        <div class="cards2"><a href="informacao.php?id=<?= $produto['id'] ?>" class="categoria2"><img class="img_2" src="<?= e($produto['imagem']) ?>" alt="<?= e($produto['nome']) ?>"><p class="cat_txt2"><?= e($produto['nome']) ?></p></a></div>
        <?php endforeach; ?>
    </div>

</body>
</html>
