<?php
require 'includes/conexao.php';

// Pega o ID que veio na URL (ex: ?id=1) e busca o produto no banco
$id = (int) ($_GET['id'] ?? 0);
$consulta = $pdo->prepare('SELECT p.*, c.nome AS categoria FROM produtos p JOIN categorias c ON c.id = p.categoria_id WHERE p.id = ? AND p.ativo = 1');
$consulta->execute([$id]);
$produto = $consulta->fetch();

if (!$produto) {
    http_response_code(404);
}

require 'includes/cabecalho.php';
?>

    <div class="detalhes">
        <?php if ($produto): ?>
        <img src="<?= e($produto['imagem']) ?>" alt="<?= e($produto['nome']) ?>" class="info_img">
        <h2 class="info_titulo"><?= e($produto['nome']) ?></h2>
        <ul class="lista_desc">
            <li class="info_desc">Valor: <?= $produto['preco'] !== null ? 'R$ ' . number_format($produto['preco'], 2, ',', '.') : 'sob consulta' ?></li>
            <li class="info_desc">Cor: <?= e($produto['cores'] ?? 'Sem opções') ?></li>
            <li class="info_desc">Opções: <?= e($produto['opcoes'] ?? 'Sem opções') ?></li>
            <li class="info_desc">Categoria: <?= e($produto['categoria']) ?></li>
        </ul>
        <?php else: ?>
        <h2 class="info_titulo">Produto não encontrado</h2>
        <ul class="lista_desc">
            <li class="info_desc">Desculpe, não conseguimos localizar os dados deste item.</li>
        </ul>
        <?php endif; ?>
        <a href="catalogo.php" class="voltar">Voltar ao Catálogo</a>
    </div>

</body>
</html>
