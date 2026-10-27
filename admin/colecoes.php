<?php
/**
 * admin/colecoes.php - Lista das colecoes
 *
 * Quais produtos entram em cada colecao e escolhido na tela do PRODUTO
 * (checkboxes em produto_form.php), porque um produto pode estar em varias.
 */

require __DIR__ . '/proteger.php';

$colecoes = $pdo->query(
    'SELECT c.id, c.nome, c.slug, c.imagem, c.ordem, c.ativo,
            (SELECT COUNT(*) FROM colecao_produto cp WHERE cp.colecao_id = c.id) AS quantidade
       FROM colecoes c
      ORDER BY c.ordem ASC, c.nome ASC'
)->fetchAll();

$tituloAdmin = 'Colecoes';
$paginaAtiva = 'colecoes';
require __DIR__ . '/includes/header.php';
?>

<div class="acoes-topo">
  <p style="margin:0;color:var(--texto-suave);font-size:.9rem">
    Para colocar um produto numa colecao, edite o produto e marque a colecao la.
  </p>
  <a class="botao" style="width:auto;padding:11px 22px"
     href="<?= e(url('admin/colecao_form.php')) ?>">+ Nova colecao</a>
</div>

<?php if ($colecoes): ?>
  <div class="tabela-rolagem">
    <table class="lista">
      <thead>
        <tr>
          <th>Imagem</th><th>Nome</th><th>Endereco (slug)</th>
          <th>Pecas</th><th>Ordem</th><th>Situacao</th><th>Acoes</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($colecoes as $colecao): ?>
          <tr>
            <td>
              <img class="miniatura" src="<?= e(urlUpload('colecoes', $colecao['imagem'])) ?>"
                   alt="Imagem da colecao <?= e($colecao['nome']) ?>" loading="lazy">
            </td>
            <td><?= e($colecao['nome']) ?></td>
            <td><code><?= e($colecao['slug']) ?></code></td>
            <td><?= (int) $colecao['quantidade'] ?></td>
            <td><?= (int) $colecao['ordem'] ?></td>
            <td>
              <span class="etiqueta <?= $colecao['ativo'] ? 'etiqueta-ativo' : 'etiqueta-inativo' ?>">
                <?= $colecao['ativo'] ? 'no ar' : 'escondida' ?>
              </span>
            </td>
            <td>
              <div class="acoes-linha">
                <a class="botao-pequeno"
                   href="<?= e(url('admin/colecao_form.php?id=' . (int) $colecao['id'])) ?>">Editar</a>

                <form method="post" action="<?= e(url('admin/colecao_excluir.php')) ?>">
                  <?= csrfCampo() ?>
                  <input type="hidden" name="acao" value="alternar">
                  <input type="hidden" name="id" value="<?= (int) $colecao['id'] ?>">
                  <button type="submit" class="botao-pequeno">
                    <?= $colecao['ativo'] ? 'Esconder' : 'Publicar' ?>
                  </button>
                </form>

                <form method="post" action="<?= e(url('admin/colecao_excluir.php')) ?>"
                      onsubmit="return confirm('Excluir esta colecao? Os produtos nao sao apagados.');">
                  <?= csrfCampo() ?>
                  <input type="hidden" name="acao" value="excluir">
                  <input type="hidden" name="id" value="<?= (int) $colecao['id'] ?>">
                  <button type="submit" class="botao-pequeno perigo">Excluir</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php else: ?>
  <div class="cartao">
    <h2>Nenhuma colecao cadastrada</h2>
    <p>Colecoes sao kits e conjuntos, como "Dupla Pinguim". Crie a primeira.</p>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
