<?php
/**
 * admin/includes/header.php - Topo de todas as paginas do painel
 *
 * Antes de incluir, a pagina define:
 *   $tituloAdmin  - titulo que aparece na aba e no topo
 *   $paginaAtiva  - qual item do menu lateral fica destacado
 *                   (inicio, produtos, categorias, colecoes, banners, pedidos, senha)
 */

$tituloAdmin = $tituloAdmin ?? 'Painel';
$paginaAtiva = $paginaAtiva ?? '';
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($tituloAdmin) ?> - Painel <?= e(NOME_SITE) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
<link rel="stylesheet" href="<?= e(url('admin/assets/admin.css')) ?>">
<link rel="icon" href="<?= e(url('assets/img/favicon.svg')) ?>" type="image/svg+xml">
</head>
<body class="painel">

<div class="fundo-menu" id="fundoMenu"></div>

<?php require __DIR__ . '/menu.php'; ?>

<div class="painel-conteudo">
  <header class="painel-topo">
    <button type="button" class="botao-icone" id="abrirMenu" aria-label="Abrir menu" aria-expanded="false">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true">
        <line x1="3" y1="6"  x2="21" y2="6"></line>
        <line x1="3" y1="12" x2="21" y2="12"></line>
        <line x1="3" y1="18" x2="21" y2="18"></line>
      </svg>
      Menu
    </button>

    <h1><?= e($tituloAdmin) ?></h1>

    <a class="painel-ver-site" href="<?= e(url('index.php')) ?>" target="_blank" rel="noopener">
      Ver o site
    </a>
  </header>

  <main class="painel-corpo">
    <?php mostrarFlash(); ?>
