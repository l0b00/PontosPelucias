<?php
/**
 * colecao.php - Lista os produtos de uma colecao
 * Endereco: colecao.php?slug=dupla-pinguim
 *
 * A diferenca para categoria.php e que um produto pode estar em varias
 * colecoes, entao a consulta passa pela tabela colecao_produto (N:N).
 */

require __DIR__ . '/includes/conexao.php';
require __DIR__ . '/includes/funcoes.php';

$slug = trim($_GET['slug'] ?? '');

$consulta = $pdo->prepare(
    'SELECT id, nome, slug, descricao, imagem FROM colecoes WHERE slug = ? AND ativo = 1'
);
$consulta->execute([$slug]);
$colecao = $consulta->fetch();

if (!$colecao) {
    require __DIR__ . '/404.php';
    exit;
}

$pagina    = paginaAtual();
$porPagina = PRODUTOS_POR_PAGINA;
$inicio    = ($pagina - 1) * $porPagina;

$contagem = $pdo->prepare(
    'SELECT COUNT(*) FROM produtos p
       INNER JOIN colecao_produto cp ON cp.produto_id = p.id
      WHERE p.ativo = 1 AND cp.colecao_id = ?'
);
$contagem->execute([$colecao['id']]);
$total = (int) $contagem->fetchColumn();
$totalPaginas = (int) ceil($total / $porPagina);

$sql = selectDeCards()
     . ' INNER JOIN colecao_produto cp ON cp.produto_id = p.id'
     . ' WHERE p.ativo = 1 AND cp.colecao_id = :colecao'
     . ' ORDER BY ' . ordemSql($_GET['ordem'] ?? null)
     . ' LIMIT :inicio, :limite';

$consulta = $pdo->prepare($sql);
$consulta->bindValue(':colecao', $colecao['id'], PDO::PARAM_INT);
$consulta->bindValue(':inicio', $inicio, PDO::PARAM_INT);
$consulta->bindValue(':limite', $porPagina, PDO::PARAM_INT);
$consulta->execute();
$produtos = $consulta->fetchAll();

$arquivoAtual  = 'colecao.php';
$parametrosUrl = ['slug' => $colecao['slug']];
$mensagemVazia = 'Esta colecao ainda nao tem pecas cadastradas.';

$tituloPagina = 'Colecao ' . $colecao['nome'] . ' - ' . NOME_SITE;
require __DIR__ . '/includes/header.php';
?>

<div class="container">
  <p class="caminho">
    <a href="<?= e(url('index.php')) ?>">Inicio</a> &rsaquo;
    <a href="<?= e(url('colecoes.php')) ?>">Colecoes</a> &rsaquo; <?= e($colecao['nome']) ?>
  </p>

  <div class="cabecalho-pagina">
    <h1><?= e($colecao['nome']) ?></h1>
    <?php if (!empty($colecao['descricao'])): ?>
      <p><?= e($colecao['descricao']) ?></p>
    <?php endif; ?>
    <p><?= $total ?> <?= $total === 1 ? 'produto' : 'produtos' ?></p>
  </div>

  <?php require __DIR__ . '/includes/lista_produtos.php'; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
