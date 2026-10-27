<?php
/**
 * index.php - Pagina inicial
 *
 * Mostra, na mesma ordem do prototipo:
 *   1. carrossel de banners        4. bloco de colecoes
 *   2. faixa de diferenciais       5. mais vendidos
 *   3. categorias
 */

$tituloPagina = 'Ponntos & Pelucias by Carol - amigurumis feitos a mao';
require __DIR__ . '/includes/header.php';

// --- Dados que a pagina precisa -------------------------------------------
$banners = $pdo->query(
    'SELECT titulo, imagem, link FROM banners
      WHERE ativo = 1 ORDER BY ordem ASC, id ASC'
)->fetchAll();

$colecoes = $pdo->query(
    'SELECT nome, slug, imagem FROM colecoes
      WHERE ativo = 1 ORDER BY ordem ASC, nome ASC'
)->fetchAll();

// Os produtos marcados como "mais vendido" no painel admin.
$maisVendidos = $pdo->query(
    selectDeCards() . ' WHERE p.ativo = 1 AND p.mais_vendido = 1
                        ORDER BY p.criado_em DESC LIMIT 8'
)->fetchAll();

// $categoriasDoMenu ja foi carregado pelo header.php.
$categorias = $categoriasDoMenu;
?>

<!-- 1. CARROSSEL DE BANNERS ---------------------------------------------- -->
<?php if ($banners): ?>
<section class="carrossel" data-auto="5000" aria-label="Destaques">
  <div class="carrossel-trilho">
    <?php foreach ($banners as $banner): ?>
      <div class="carrossel-item">
        <?php if (!empty($banner['link'])): ?>
          <a href="<?= e(urlOuExterna($banner['link'])) ?>">
            <img src="<?= e(urlUpload('banners', $banner['imagem'])) ?>"
                 alt="<?= e($banner['titulo']) ?>">
          </a>
        <?php else: ?>
          <img src="<?= e(urlUpload('banners', $banner['imagem'])) ?>"
               alt="<?= e($banner['titulo']) ?>">
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>

  <button type="button" class="carrossel-seta anterior" aria-label="Imagem anterior">&lt;</button>
  <button type="button" class="carrossel-seta proxima"  aria-label="Proxima imagem">&gt;</button>
</section>
<?php endif; ?>

<!-- 2. FAIXA DE DIFERENCIAIS --------------------------------------------- -->
<!-- Card branco que sobe por cima do fim do banner (ver .diferenciais no CSS) -->
<section class="diferenciais container" aria-label="Como funciona">
  <div class="diferencial">
    <!-- agulha de croche -->
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
      <path d="M4 20 16 8"></path>
      <path d="M16 8c1-1 2.5-1 3.5 0s1 2.5 0 3.5L18 13"></path>
      <circle cx="5.2" cy="18.8" r="1.6"></circle>
    </svg>
    <div>
      <strong>100% artesanal</strong>
      <span>produtos feitos manualmente</span>
    </div>
  </div>

  <div class="diferencial">
    <!-- simbolo do Pix (losango formado por quatro pontas) -->
    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
      <path d="M12 2.6 15.4 6 12 9.4 8.6 6 12 2.6Zm-6 6L9.4 12 6 15.4 2.6 12 6 8.6Zm12 0L21.4 12 18 15.4 14.6 12 18 8.6ZM12 14.6 15.4 18 12 21.4 8.6 18 12 14.6Z"/>
    </svg>
    <div>
      <strong>Pagamento</strong>
      <span>somente Pix</span>
    </div>
  </div>

  <div class="diferencial">
    <!-- caixa de encomenda -->
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" aria-hidden="true">
      <path d="M3 7.5 12 3l9 4.5v9L12 21l-9-4.5v-9Z"></path>
      <path d="M3 7.5 12 12l9-4.5M12 12v9"></path>
    </svg>
    <div>
      <strong>Entrega pelos Correios</strong>
      <span>do atelie direto para sua casa</span>
    </div>
  </div>
</section>

<!-- 3. CATEGORIAS --------------------------------------------------------- -->
<?php if ($categorias): ?>
<section aria-labelledby="tituloCategorias">
  <h2 class="titulo-secao" id="tituloCategorias">Categorias</h2>

  <div class="lista-categorias">
    <?php foreach ($categorias as $categoria): ?>
      <a class="categoria-item" href="<?= e(url('categoria.php?slug=' . urlencode($categoria['slug']))) ?>">
        <img src="<?= e(urlUpload('categorias', $categoria['imagem'])) ?>"
             alt="<?= e($categoria['nome']) ?>" loading="lazy" width="88" height="88">
        <span><?= e(mb_strtolower($categoria['nome'], 'UTF-8')) ?></span>
      </a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- 4. COLECOES ----------------------------------------------------------- -->
<?php if ($colecoes): ?>
<section aria-label="Colecoes">
  <div class="bloco-colecoes">
    <a class="cartao-colecoes" href="<?= e(url('colecoes.php')) ?>">
      <strong>Colecoes</strong>
      <span>Kits e conjuntos dos amigurumis mais amados em um mesmo lugar</span>
    </a>

    <div class="carrossel carrossel-colecoes" data-auto="5000"
         data-bolinhas="#bolinhasColecoes" aria-label="Imagens das colecoes">
      <div class="carrossel-trilho">
        <?php foreach ($colecoes as $colecao): ?>
          <div class="carrossel-item">
            <a href="<?= e(url('colecao.php?slug=' . urlencode($colecao['slug']))) ?>">
              <img src="<?= e(urlUpload('colecoes', $colecao['imagem'])) ?>"
                   alt="Colecao <?= e($colecao['nome']) ?>" loading="lazy">
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <!-- As bolinhas sao criadas pelo JavaScript (ver data-bolinhas acima) -->
  <div class="bolinhas" id="bolinhasColecoes"></div>
</section>
<?php endif; ?>

<!-- 5. MAIS VENDIDOS ------------------------------------------------------ -->
<?php if ($maisVendidos): ?>
<section aria-labelledby="tituloMaisVendidos">
  <h2 class="titulo-secao" id="tituloMaisVendidos">Mais vendidos</h2>

  <div class="vitrine">
    <?php foreach ($maisVendidos as $produto): ?>
      <a class="card-produto" href="<?= e(url('produto.php?id=' . (int) $produto['id'])) ?>">
        <img src="<?= e(urlUpload('produtos', $produto['foto'])) ?>"
             alt="<?= e($produto['nome']) ?>" loading="lazy">
        <div class="card-corpo">
          <p class="card-nome"><?= e($produto['nome']) ?></p>
          <span class="card-preco"><?= e(formatarPreco($produto['preco'])) ?></span>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
