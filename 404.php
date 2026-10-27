<?php
/**
 * 404.php - Pagina mostrada quando o produto, a categoria ou a colecao
 *           nao existe (ou foi desativada no painel).
 *
 * Pode ser aberta direto pelo navegador ou incluida por outra pagina:
 *     require __DIR__ . '/404.php';
 *     exit;
 */

require_once __DIR__ . '/includes/conexao.php';
require_once __DIR__ . '/includes/funcoes.php';

// Avisa o navegador (e o Google) que a pagina realmente nao existe.
http_response_code(404);

$tituloPagina = 'Pagina nao encontrada - ' . NOME_SITE;
require __DIR__ . '/includes/header.php';

// Sugere algumas pecas para a pessoa nao sair do site de maos vazias.
$sugestoes = $pdo->query(
    selectDeCards() . ' WHERE p.ativo = 1 ORDER BY p.mais_vendido DESC, p.criado_em DESC LIMIT 4'
)->fetchAll();
?>

<div class="container">
  <div class="vazio" style="padding-top:60px">
    <strong>Ops! Nao achamos essa pagina</strong>
    <p>
      A peca que voce procurou pode ter saido do catalogo ou o endereco veio
      com algum erro de digitacao.
    </p>
    <p>
      <a class="botao" href="<?= e(url('index.php')) ?>">Voltar para a home</a>
    </p>
  </div>

  <?php if ($sugestoes): ?>
    <section aria-labelledby="tituloSugestoes">
      <h2 class="titulo-secao" id="tituloSugestoes">Quem sabe uma destas</h2>
      <div class="grade-produtos">
        <?php foreach ($sugestoes as $sugestao): ?>
          <a class="card-produto" href="<?= e(url('produto.php?id=' . (int) $sugestao['id'])) ?>">
            <img src="<?= e(urlUpload('produtos', $sugestao['foto'])) ?>"
                 alt="<?= e($sugestao['nome']) ?>" loading="lazy">
            <div class="card-corpo">
              <p class="card-nome"><?= e($sugestao['nome']) ?></p>
              <span class="card-preco"><?= e(formatarPreco($sugestao['preco'])) ?></span>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
