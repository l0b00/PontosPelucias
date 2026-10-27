<?php
/**
 * busca.php - Resultado da barra de pesquisa
 * Endereco: busca.php?q=pinguim
 *
 * Procura o texto no nome E na descricao, somente entre produtos ativos.
 */

require __DIR__ . '/includes/conexao.php';
require __DIR__ . '/includes/funcoes.php';

$termo = trim($_GET['q'] ?? '');
// Limita o tamanho para ninguem mandar um texto gigante de proposito.
$termo = mb_substr($termo, 0, 80, 'UTF-8');

$produtos = [];
$total = 0;
$totalPaginas = 0;
$pagina = paginaAtual();

if ($termo !== '') {
    // Os caracteres % e _ tem significado especial no LIKE; a barra invertida
    // antes deles faz o MySQL tratar como texto comum.
    $procura = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $termo) . '%';

    $porPagina = PRODUTOS_POR_PAGINA;
    $inicio = ($pagina - 1) * $porPagina;

    $contagem = $pdo->prepare(
        'SELECT COUNT(*) FROM produtos p
          WHERE p.ativo = 1 AND (p.nome LIKE ? OR p.descricao LIKE ?)'
    );
    $contagem->execute([$procura, $procura]);
    $total = (int) $contagem->fetchColumn();
    $totalPaginas = (int) ceil($total / $porPagina);

    $sql = selectDeCards()
         . ' WHERE p.ativo = 1 AND (p.nome LIKE :termo OR p.descricao LIKE :termo2)'
         . ' ORDER BY ' . ordemSql($_GET['ordem'] ?? null)
         . ' LIMIT :inicio, :limite';

    $consulta = $pdo->prepare($sql);
    $consulta->bindValue(':termo', $procura);
    $consulta->bindValue(':termo2', $procura);
    $consulta->bindValue(':inicio', $inicio, PDO::PARAM_INT);
    $consulta->bindValue(':limite', $porPagina, PDO::PARAM_INT);
    $consulta->execute();
    $produtos = $consulta->fetchAll();
}

$arquivoAtual  = 'busca.php';
$parametrosUrl = ['q' => $termo];
$mensagemVazia = 'Nao encontramos nenhuma peca com esse nome. Tente outra palavra, '
               . 'como "pinguim", "chaveiro" ou "gatinho".';

$tituloPagina = 'Busca por "' . $termo . '" - ' . NOME_SITE;
require __DIR__ . '/includes/header.php';
?>

<div class="container">
  <p class="caminho">
    <a href="<?= e(url('index.php')) ?>">Inicio</a> &rsaquo; Busca
  </p>

  <div class="cabecalho-pagina">
    <h1>Busca</h1>
    <?php if ($termo === ''): ?>
      <p>Digite alguma coisa na barra de pesquisa la em cima.</p>
    <?php else: ?>
      <p><?= $total ?> <?= $total === 1 ? 'resultado' : 'resultados' ?>
         para &ldquo;<?= e($termo) ?>&rdquo;</p>
    <?php endif; ?>
  </div>

  <?php if ($termo !== ''): ?>
    <?php require __DIR__ . '/includes/lista_produtos.php'; ?>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
