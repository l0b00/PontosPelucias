<?php
/**
 * lista_produtos.php - A grade de produtos com filtro de ordem e paginacao
 *
 * As paginas categoria.php, colecao.php e busca.php mostram exatamente a mesma
 * lista, entao o HTML dela mora aqui e nao e copiado tres vezes.
 *
 * Antes de incluir, a pagina precisa definir:
 *   $produtos       - array vindo do banco (id, nome, preco, foto, mais_vendido)
 *   $total          - quantidade total de produtos encontrados
 *   $pagina         - pagina atual
 *   $totalPaginas   - quantas paginas existem
 *   $arquivoAtual   - 'categoria.php', 'colecao.php' ou 'busca.php'
 *   $parametrosUrl  - o que deve continuar na URL (ex.: ['slug' => 'animais'])
 *   $mensagemVazia  - texto amigavel quando nao ha nenhum produto
 */

$ordemAtual = $_GET['ordem'] ?? 'recentes';
if (!array_key_exists($ordemAtual, opcoesDeOrdem())) {
    $ordemAtual = 'recentes';
}
?>

<?php if ($total > 0): ?>
  <form class="filtros" method="get" action="<?= e(url($arquivoAtual)) ?>">
    <?php // Mantem o slug / a busca ao trocar a ordem ?>
    <?php foreach ($parametrosUrl as $nome => $valor): ?>
      <input type="hidden" name="<?= e($nome) ?>" value="<?= e((string) $valor) ?>">
    <?php endforeach; ?>

    <label for="ordem">Ordenar por:</label>
    <select name="ordem" id="ordem">
      <?php foreach (opcoesDeOrdem() as $chave => $rotulo): ?>
        <option value="<?= e($chave) ?>" <?= $chave === $ordemAtual ? 'selected' : '' ?>>
          <?= e($rotulo) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="botao botao-secundario" style="width:auto;padding:7px 16px;font-size:.85rem">
      Aplicar
    </button>
  </form>

  <div class="grade-produtos">
    <?php foreach ($produtos as $produto): ?>
      <a class="card-produto" href="<?= e(url('produto.php?id=' . (int) $produto['id'])) ?>">
        <?php if (!empty($produto['mais_vendido'])): ?>
          <span class="selo-mais-vendido">mais vendido</span>
        <?php endif; ?>
        <img src="<?= e(urlUpload('produtos', $produto['foto'])) ?>"
             alt="<?= e($produto['nome']) ?>" loading="lazy">
        <div class="card-corpo">
          <p class="card-nome"><?= e($produto['nome']) ?></p>
          <span class="card-preco"><?= e(formatarPreco($produto['preco'])) ?></span>
        </div>
      </a>
    <?php endforeach; ?>
  </div>

  <?php
  // A paginacao precisa levar junto o slug/busca e a ordem escolhida.
  $parametrosPaginacao = $parametrosUrl;
  $parametrosPaginacao['ordem'] = $ordemAtual;
  imprimirPaginacao($arquivoAtual, $parametrosPaginacao, $pagina, $totalPaginas);
  ?>

<?php else: ?>
  <div class="vazio">
    <strong>Nada por aqui ainda</strong>
    <p><?= e($mensagemVazia) ?></p>
    <p>
      <a class="botao botao-secundario" href="<?= e(url('index.php')) ?>">Voltar para a home</a>
    </p>
  </div>
<?php endif; ?>
