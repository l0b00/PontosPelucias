<?php
require 'includes/conexao.php';

// Se veio ?categoria=N (clique num card da página inicial), mostra só essa categoria
$categoriaId = (int) ($_GET['categoria'] ?? 0);

if ($categoriaId > 0) {
    $consulta = $pdo->prepare('SELECT id, nome, imagem FROM produtos WHERE ativo = 1 AND categoria_id = ? ORDER BY id');
    $consulta->execute([$categoriaId]);
    $produtos = $consulta->fetchAll();
} else {
    $produtos = $pdo->query('SELECT id, nome, imagem FROM produtos WHERE ativo = 1 ORDER BY id')->fetchAll();
}

require 'includes/cabecalho.php';
?>

    <?php // A quantidade de cards por linha se ajusta à largura da tela (ver .catalogo no style.css) ?>
    <div class="catalogo">
        <?php foreach ($produtos as $produto): ?>
        <div class="cards2"><a href="informacao.php?id=<?= $produto['id'] ?>" class="categoria2"><img class="img_2" src="<?= e($produto['imagem']) ?>" alt="<?= e($produto['nome']) ?>"><p class="cat_txt2"><?= e($produto['nome']) ?></p></a></div>
        <?php endforeach; ?>
        <?php if (!$produtos): ?>
        <p class="cat_txt2">Nenhum produto nesta categoria por enquanto.</p>
        <?php endif; ?>
    </div>

    <?php include 'rodape.php';?>
    
</body>
</html>
