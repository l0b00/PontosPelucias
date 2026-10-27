<?php
/**
 * produto.php - Pagina de um produto
 * Endereco: produto.php?id=3
 *
 * Mostra a galeria de fotos, a ficha da peca e o formulario de encomenda.
 * Quem recebe o formulario e o arquivo encomendar.php.
 */

require __DIR__ . '/includes/conexao.php';
require __DIR__ . '/includes/funcoes.php';

$id = (int) ($_GET['id'] ?? 0);

// --- Busca o produto (com o nome da categoria junto) ----------------------
$consulta = $pdo->prepare(
    'SELECT p.*, c.nome AS categoria_nome, c.slug AS categoria_slug
       FROM produtos p
       LEFT JOIN categorias c ON c.id = p.categoria_id
      WHERE p.id = ? AND p.ativo = 1'
);
$consulta->execute([$id]);
$produto = $consulta->fetch();

// Produto inexistente ou desativado: pagina 404 amigavel.
if (!$produto) {
    require __DIR__ . '/404.php';
    exit;
}

// --- Fotos: a principal vem primeiro --------------------------------------
$consulta = $pdo->prepare(
    'SELECT arquivo FROM produto_fotos
      WHERE produto_id = ?
      ORDER BY principal DESC, ordem ASC, id ASC'
);
$consulta->execute([$produto['id']]);
$fotos = $consulta->fetchAll();

// --- Voce tambem pode gostar: outras pecas da mesma categoria -------------
$relacionados = [];
if (!empty($produto['categoria_id'])) {
    $consulta = $pdo->prepare(
        selectDeCards() . ' WHERE p.ativo = 1 AND p.categoria_id = ? AND p.id <> ?
                            ORDER BY RAND() LIMIT 4'
    );
    $consulta->execute([$produto['categoria_id'], $produto['id']]);
    $relacionados = $consulta->fetchAll();
}

// Se o envio do formulario deu erro, encomendar.php guardou o que a pessoa
// tinha digitado para nao precisar escrever tudo de novo.
$dadosAntigos = $_SESSION['encomenda_dados'] ?? [];
unset($_SESSION['encomenda_dados']);
$houveErro = !empty($_SESSION['flash']) && $_SESSION['flash']['tipo'] === 'erro';

$tituloPagina    = $produto['nome'] . ' - ' . NOME_SITE;
$descricaoPagina = mb_substr(strip_tags($produto['descricao'] ?? ''), 0, 150, 'UTF-8');
require __DIR__ . '/includes/header.php';
?>

<div class="container produto">
  <p class="caminho">
    <a href="<?= e(url('index.php')) ?>">Inicio</a>
    <?php if (!empty($produto['categoria_slug'])): ?>
      &rsaquo; <a href="<?= e(url('categoria.php?slug=' . urlencode($produto['categoria_slug']))) ?>">
        <?= e($produto['categoria_nome']) ?></a>
    <?php endif; ?>
    &rsaquo; <?= e($produto['nome']) ?>
  </p>

  <?php mostrarFlash(); ?>

  <div class="produto-colunas">

    <!-- GALERIA ---------------------------------------------------------- -->
    <div>
      <div class="galeria-principal">
        <img id="fotoPrincipal" src="<?= e(urlUpload('produtos', $fotos[0]['arquivo'] ?? null)) ?>"
             alt="<?= e($produto['nome']) ?>">
      </div>

      <?php if (count($fotos) > 1): ?>
        <div class="miniaturas">
          <?php foreach ($fotos as $indice => $foto): ?>
            <button type="button" class="<?= $indice === 0 ? 'ativa' : '' ?>"
                    data-foto="<?= e(urlUpload('produtos', $foto['arquivo'])) ?>"
                    data-alt="<?= e($produto['nome']) ?> - foto <?= $indice + 1 ?>">
              <img src="<?= e(urlUpload('produtos', $foto['arquivo'])) ?>"
                   alt="Ver foto <?= $indice + 1 ?> de <?= e($produto['nome']) ?>" loading="lazy">
            </button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- INFORMACOES ------------------------------------------------------ -->
    <div>
      <h1><?= e($produto['nome']) ?></h1>

      <?php if (!empty($produto['categoria_nome'])): ?>
        <p class="produto-categoria">
          em <a href="<?= e(url('categoria.php?slug=' . urlencode($produto['categoria_slug']))) ?>">
            <?= e($produto['categoria_nome']) ?></a>
        </p>
      <?php endif; ?>

      <span class="produto-preco"><?= e(formatarPreco($produto['preco'])) ?></span>

      <?php if (!empty($produto['descricao'])): ?>
        <p><?= nl2br(e($produto['descricao'])) ?></p>
      <?php endif; ?>

      <ul class="ficha">
        <?php if (!empty($produto['materiais'])): ?>
          <li><strong>Materiais</strong><?= e($produto['materiais']) ?></li>
        <?php endif; ?>
        <?php if (!empty($produto['tamanho'])): ?>
          <li><strong>Tamanho aproximado</strong><?= e($produto['tamanho']) ?></li>
        <?php endif; ?>
        <li><strong>Pagamento</strong>Somente Pix, combinado com a Carol</li>
        <li><strong>Entrega</strong>Pelos Correios, do atelie direto para sua casa</li>
      </ul>

      <p class="aviso-artesanal">
        Cada peca e feita a mao e e unica &mdash; pode haver pequenas variacoes
        de cor, tamanho e acabamento em relacao a foto.
      </p>

      <!-- ENCOMENDA ------------------------------------------------------ -->
      <!-- O botao e escondido pelo JavaScript e so aparece o formulario depois
           do clique. Sem JavaScript, o formulario ja fica visivel. -->
      <button type="button" id="abrirEncomenda" class="botao botao-whatsapp" hidden>
        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21h.01c5.46 0 9.9-4.45 9.9-9.91C21.95 6.45 17.5 2 12.04 2Zm4.52 12.15c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.13-.16.24-.64.8-.78.97-.14.16-.29.19-.54.06-.25-.12-1.05-.39-1.99-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.01-.38.11-.5.11-.11.25-.29.37-.43.13-.15.17-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.4-.42-.56-.43h-.48c-.17 0-.43.06-.66.31-.23.25-.86.85-.86 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.23 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.47-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.15-1.18-.06-.1-.23-.16-.48-.29Z"/>
        </svg>
        Encomendar pelo WhatsApp
      </button>

      <form class="formulario" id="formEncomenda" method="post"
            action="<?= e(url('encomendar.php')) ?>"
            <?= $houveErro ? 'data-manter-aberto="1"' : '' ?>>
        <?= csrfCampo() ?>
        <input type="hidden" name="produto_id" value="<?= (int) $produto['id'] ?>">

        <h2>Encomendar pelo WhatsApp</h2>
        <p style="font-size:.85rem;color:var(--texto-suave);margin-top:0">
          Preencha os dados e o WhatsApp abre com a mensagem pronta para a Carol.
        </p>

        <div class="campo">
          <label for="nome_cliente">Seu nome</label>
          <input type="text" id="nome_cliente" name="nome_cliente" maxlength="120" required
                 value="<?= e($dadosAntigos['nome_cliente'] ?? '') ?>">
        </div>

        <div class="campo">
          <label for="telefone">Seu WhatsApp (com DDD)</label>
          <input type="tel" id="telefone" name="telefone" maxlength="20" required
                 placeholder="(11) 98888-7777"
                 value="<?= e($dadosAntigos['telefone'] ?? '') ?>">
        </div>

        <div class="campo">
          <label for="quantidade">Quantidade</label>
          <input type="number" id="quantidade" name="quantidade" min="1" max="99" required
                 value="<?= e((string) ($dadosAntigos['quantidade'] ?? 1)) ?>">
        </div>

        <div class="campo">
          <label for="observacao">Observacao (opcional)</label>
          <textarea id="observacao" name="observacao" maxlength="500"
                    placeholder="Cores, data que precisa, alguma personalizacao..."><?= e($dadosAntigos['observacao'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="botao botao-whatsapp">Enviar e abrir o WhatsApp</button>
      </form>
    </div>
  </div>

  <!-- VOCE TAMBEM PODE GOSTAR ------------------------------------------- -->
  <?php if ($relacionados): ?>
    <section aria-labelledby="tituloRelacionados">
      <h2 class="titulo-secao" id="tituloRelacionados">Voce tambem pode gostar</h2>
      <div class="grade-produtos">
        <?php foreach ($relacionados as $outro): ?>
          <a class="card-produto" href="<?= e(url('produto.php?id=' . (int) $outro['id'])) ?>">
            <img src="<?= e(urlUpload('produtos', $outro['foto'])) ?>"
                 alt="<?= e($outro['nome']) ?>" loading="lazy">
            <div class="card-corpo">
              <p class="card-nome"><?= e($outro['nome']) ?></p>
              <span class="card-preco"><?= e(formatarPreco($outro['preco'])) ?></span>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
