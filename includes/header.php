<?php
/**
 * header.php - Topo de todas as paginas do site publico
 *
 * Antes de incluir este arquivo, a pagina pode definir:
 *   $tituloPagina     - o que aparece na aba do navegador
 *   $descricaoPagina  - resumo para o Google (opcional)
 *
 * Ele ja carrega a conexao e as funcoes, entao as paginas so precisam de:
 *   require __DIR__ . '/includes/header.php';
 */

require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/funcoes.php';

$tituloPagina    = $tituloPagina    ?? NOME_SITE;
$descricaoPagina = $descricaoPagina ?? 'Amigurumis e pelucias de croche feitos a mao pela Carol.';

// O menu lateral mostra as categorias cadastradas no admin.
$categoriasDoMenu = categoriasAtivas($pdo);
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($tituloPagina) ?></title>
<meta name="description" content="<?= e($descricaoPagina) ?>">

<!-- Fontes do Google: Fredoka nos titulos, Nunito no texto -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
<link rel="icon" href="<?= e(url('assets/img/favicon.svg')) ?>" type="image/svg+xml">
</head>
<body>

<!-- Fundo escuro que aparece junto com o menu lateral -->
<div class="fundo-menu" id="fundoMenu"></div>

<!-- Menu lateral (abre pelo botao hamburguer) -->
<nav class="menu-lateral" id="menuLateral" aria-label="Menu principal">
  <button type="button" class="menu-fechar" id="fecharMenu" aria-label="Fechar menu">&times;</button>
  <h2>Menu</h2>
  <ul>
    <li><a href="<?= e(url('index.php')) ?>">Inicio</a></li>
    <li><a href="<?= e(url('colecoes.php')) ?>">Colecoes</a></li>
    <li><a href="<?= e(url('sobre.php')) ?>">Sobre a Carol</a></li>
  </ul>

  <h2 style="margin-top:1.4rem">Categorias</h2>
  <ul>
    <?php foreach ($categoriasDoMenu as $categoriaMenu): ?>
      <li>
        <a href="<?= e(url('categoria.php?slug=' . urlencode($categoriaMenu['slug']))) ?>">
          <?= e($categoriaMenu['nome']) ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>

  <h2 style="margin-top:1.4rem">Fale com a Carol</h2>
  <ul>
    <li><a href="<?= e(linkWhatsApp('Oi, Carol! Vim pelo site.')) ?>" target="_blank" rel="noopener">WhatsApp</a></li>
    <li><a href="https://instagram.com/<?= e(INSTAGRAM) ?>" target="_blank" rel="noopener">Instagram</a></li>
  </ul>
</nav>

<header class="cabecalho">
  <div class="cabecalho-topo">

    <!-- Botao do menu (hamburguer) -->
    <button type="button" class="botao-icone" id="abrirMenu" aria-label="Abrir menu" aria-expanded="false">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true">
        <line x1="3" y1="6"  x2="21" y2="6"></line>
        <line x1="3" y1="12" x2="21" y2="12"></line>
        <line x1="3" y1="18" x2="21" y2="18"></line>
      </svg>
      Menu
    </button>

    <!-- Logo em texto, com a fonte Fredoka -->
    <a class="logo" href="<?= e(url('index.php')) ?>">
      <span class="logo-nome">Ponntos &amp; Pel&uacute;cias</span>
      <span class="logo-by">by Carol</span>
    </a>

    <!-- No lugar do carrinho do prototipo: o WhatsApp da Carol -->
    <a class="botao-icone" href="<?= e(linkWhatsApp('Oi, Carol! Vim pelo site e queria saber mais.')) ?>"
       target="_blank" rel="noopener" aria-label="Falar com a Carol no WhatsApp">
      <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21h.01c5.46 0 9.9-4.45 9.9-9.91C21.95 6.45 17.5 2 12.04 2Zm0 18.15h-.01c-1.52 0-3.01-.41-4.3-1.18l-.31-.18-3.12.82.83-3.04-.2-.31a8.2 8.2 0 0 1-1.26-4.35c0-4.54 3.7-8.23 8.24-8.23 2.2 0 4.27.86 5.83 2.41a8.18 8.18 0 0 1 2.41 5.83c0 4.54-3.7 8.23-8.11 8.23Zm4.52-6.16c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.13-.16.24-.64.8-.78.97-.14.16-.29.19-.54.06-.25-.12-1.05-.39-1.99-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.01-.38.11-.5.11-.11.25-.29.37-.43.13-.15.17-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.4-.42-.56-.43h-.48c-.17 0-.43.06-.66.31-.23.25-.86.85-.86 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.23 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.47-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.15-1.18-.06-.1-.23-.16-.48-.29Z"/>
      </svg>
      WhatsApp
    </a>
  </div>

  <!-- Barra de pesquisa: manda para busca.php?q=... -->
  <div class="busca">
    <form action="<?= e(url('busca.php')) ?>" method="get" role="search">
      <label for="campoBusca" class="so-leitor">Pesquisar produtos</label>
      <input type="search" id="campoBusca" name="q" placeholder="Barra de pesquisa"
             value="<?= e($_GET['q'] ?? '') ?>" maxlength="80">
      <button type="submit" aria-label="Pesquisar">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
          <circle cx="11" cy="11" r="7"></circle>
          <line x1="16.5" y1="16.5" x2="21" y2="21"></line>
        </svg>
      </button>
    </form>
  </div>
</header>

<main>
