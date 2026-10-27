<?php
/**
 * colecoes.php - Mostra todas as colecoes
 * Cada cartao leva para colecao.php?slug=...
 */

require __DIR__ . '/includes/conexao.php';
require __DIR__ . '/includes/funcoes.php';

// Alem dos dados da colecao, conta quantos produtos ativos ela tem.
$colecoes = $pdo->query(
    'SELECT c.id, c.nome, c.slug, c.descricao, c.imagem,
            (SELECT COUNT(*) FROM colecao_produto cp
               INNER JOIN produtos p ON p.id = cp.produto_id
              WHERE cp.colecao_id = c.id AND p.ativo = 1) AS quantidade
       FROM colecoes c
      WHERE c.ativo = 1
      ORDER BY c.ordem ASC, c.nome ASC'
)->fetchAll();

$tituloPagina = 'Colecoes - ' . NOME_SITE;
require __DIR__ . '/includes/header.php';
?>

<div class="container">
  <p class="caminho">
    <a href="<?= e(url('index.php')) ?>">Inicio</a> &rsaquo; Colecoes
  </p>

  <div class="cabecalho-pagina">
    <h1>Colecoes</h1>
    <p>Kits e conjuntos dos amigurumis mais amados em um mesmo lugar</p>
  </div>

  <?php if ($colecoes): ?>
    <div class="grade-colecoes">
      <?php foreach ($colecoes as $colecao): ?>
        <a class="card-colecao" href="<?= e(url('colecao.php?slug=' . urlencode($colecao['slug']))) ?>">
          <img src="<?= e(urlUpload('colecoes', $colecao['imagem'])) ?>"
               alt="Colecao <?= e($colecao['nome']) ?>" loading="lazy">
          <div>
            <strong><?= e($colecao['nome']) ?></strong>
            <?php if (!empty($colecao['descricao'])): ?>
              <p><?= e($colecao['descricao']) ?></p>
            <?php endif; ?>
            <p><?= (int) $colecao['quantidade'] ?>
               <?= (int) $colecao['quantidade'] === 1 ? 'peca' : 'pecas' ?></p>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="vazio">
      <strong>Nenhuma colecao por enquanto</strong>
      <p>A Carol ainda esta montando os kits. Enquanto isso, veja as categorias na home.</p>
      <p><a class="botao botao-secundario" href="<?= e(url('index.php')) ?>">Voltar para a home</a></p>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
