<?php
/**
 * categoria.php - Lista os produtos de uma categoria
 * Endereco: categoria.php?slug=chaveiros&ordem=menor_preco&pagina=2
 */

require __DIR__ . '/includes/conexao.php';
require __DIR__ . '/includes/funcoes.php';

$slug = trim($_GET['slug'] ?? '');

// --- Busca a categoria ----------------------------------------------------
$consulta = $pdo->prepare('SELECT id, nome, slug FROM categorias WHERE slug = ? AND ativo = 1');
$consulta->execute([$slug]);
$categoria = $consulta->fetch();

// Categoria que nao existe (ou esta desativada) cai na pagina 404.
if (!$categoria) {
    require __DIR__ . '/404.php';
    exit;
}

// --- Monta a listagem -----------------------------------------------------
$pagina = paginaAtual();
$porPagina = PRODUTOS_POR_PAGINA;
$inicio = ($pagina - 1) * $porPagina;

// Primeiro conta quantos produtos existem, para saber o numero de paginas.
$contagem = $pdo->prepare('SELECT COUNT(*) FROM produtos WHERE ativo = 1 AND categoria_id = ?');
$contagem->execute([$categoria['id']]);
$total = (int) $contagem->fetchColumn();
$totalPaginas = (int) ceil($total / $porPagina);

// Depois busca so os produtos desta pagina.
// ordemSql() so devolve textos que nos mesmos escrevemos: o usuario nunca
// consegue injetar SQL pelo ?ordem=.
$sql = selectDeCards()
     . ' WHERE p.ativo = 1 AND p.categoria_id = :categoria'
     . ' ORDER BY ' . ordemSql($_GET['ordem'] ?? null)
     . ' LIMIT :inicio, :limite';

$consulta = $pdo->prepare($sql);
$consulta->bindValue(':categoria', $categoria['id'], PDO::PARAM_INT);
$consulta->bindValue(':inicio', $inicio, PDO::PARAM_INT);
$consulta->bindValue(':limite', $porPagina, PDO::PARAM_INT);
$consulta->execute();
$produtos = $consulta->fetchAll();

// --- Dados para o include da grade ----------------------------------------
$arquivoAtual  = 'categoria.php';
$parametrosUrl = ['slug' => $categoria['slug']];
$mensagemVazia = 'A Carol ainda nao cadastrou pecas nesta categoria. Volte em breve!';

$tituloPagina = $categoria['nome'] . ' - ' . NOME_SITE;
require __DIR__ . '/includes/header.php';
?>

<div class="container">
  <p class="caminho">
    <a href="<?= e(url('index.php')) ?>">Inicio</a> &rsaquo; <?= e($categoria['nome']) ?>
  </p>

  <div class="cabecalho-pagina">
    <h1><?= e($categoria['nome']) ?></h1>
    <p><?= $total ?> <?= $total === 1 ? 'produto' : 'produtos' ?></p>
  </div>

  <?php require __DIR__ . '/includes/lista_produtos.php'; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
