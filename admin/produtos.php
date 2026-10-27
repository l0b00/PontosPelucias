<?php
/**
 * admin/produtos.php - Lista dos produtos, com filtro por categoria e busca
 *
 * Os botoes de ativar/desativar e excluir sao formularios POST (nunca links),
 * porque acao que muda dados nao deve caber num GET.
 */

require __DIR__ . '/proteger.php';

// --- Filtros vindos da URL ------------------------------------------------
$busca       = trim($_GET['busca'] ?? '');
$categoriaId = (int) ($_GET['categoria'] ?? 0);

// Monta o WHERE aos poucos. Cada filtro acrescenta um "?" e um valor na lista:
// os valores nunca entram direto no texto do SQL.
$condicoes  = [];
$parametros = [];

if ($busca !== '') {
    $condicoes[] = 'p.nome LIKE ?';
    $parametros[] = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $busca) . '%';
}
if ($categoriaId > 0) {
    $condicoes[] = 'p.categoria_id = ?';
    $parametros[] = $categoriaId;
}

$where = $condicoes ? ' WHERE ' . implode(' AND ', $condicoes) : '';

$sql = 'SELECT p.id, p.nome, p.preco, p.mais_vendido, p.ativo,
               c.nome AS categoria_nome,
               (SELECT f.arquivo FROM produto_fotos f
                 WHERE f.produto_id = p.id
                 ORDER BY f.principal DESC, f.ordem ASC, f.id ASC LIMIT 1) AS foto
          FROM produtos p
          LEFT JOIN categorias c ON c.id = p.categoria_id'
     . $where
     . ' ORDER BY p.criado_em DESC, p.id DESC';

$consulta = $pdo->prepare($sql);
$consulta->execute($parametros);
$produtos = $consulta->fetchAll();

$categorias = $pdo->query('SELECT id, nome FROM categorias ORDER BY nome ASC')->fetchAll();

$tituloAdmin = 'Produtos';
$paginaAtiva = 'produtos';
require __DIR__ . '/includes/header.php';
?>

<div class="acoes-topo">
  <form method="get" action="<?= e(url('admin/produtos.php')) ?>">
    <label for="busca" class="so-leitor">Buscar por nome</label>
    <input type="search" id="busca" name="busca" placeholder="Buscar pelo nome"
           value="<?= e($busca) ?>" maxlength="80">

    <label for="categoria" class="so-leitor">Filtrar por categoria</label>
    <select name="categoria" id="categoria">
      <option value="0">Todas as categorias</option>
      <?php foreach ($categorias as $categoria): ?>
        <option value="<?= (int) $categoria['id'] ?>"
          <?= $categoriaId === (int) $categoria['id'] ? 'selected' : '' ?>>
          <?= e($categoria['nome']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <button type="submit" class="botao-pequeno">Filtrar</button>
    <?php if ($busca !== '' || $categoriaId > 0): ?>
      <a class="botao-pequeno" href="<?= e(url('admin/produtos.php')) ?>">Limpar</a>
    <?php endif; ?>
  </form>

  <a class="botao" style="width:auto;padding:11px 22px"
     href="<?= e(url('admin/produto_form.php')) ?>">+ Novo produto</a>
</div>

<?php if ($produtos): ?>
  <div class="tabela-rolagem">
    <table class="lista">
      <thead>
        <tr>
          <th>Foto</th>
          <th>Nome</th>
          <th>Categoria</th>
          <th>Preco</th>
          <th>Situacao</th>
          <th>Acoes</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($produtos as $produto): ?>
          <tr>
            <td>
              <img class="miniatura" src="<?= e(urlUpload('produtos', $produto['foto'])) ?>"
                   alt="Foto de <?= e($produto['nome']) ?>" loading="lazy">
            </td>
            <td>
              <?= e($produto['nome']) ?>
              <?php if ($produto['mais_vendido']): ?>
                <br><span class="etiqueta etiqueta-destaque">mais vendido</span>
              <?php endif; ?>
            </td>
            <td><?= e($produto['categoria_nome'] ?? 'sem categoria') ?></td>
            <td><?= e(formatarPreco($produto['preco'])) ?></td>
            <td>
              <span class="etiqueta <?= $produto['ativo'] ? 'etiqueta-ativo' : 'etiqueta-inativo' ?>">
                <?= $produto['ativo'] ? 'no ar' : 'escondido' ?>
              </span>
            </td>
            <td>
              <div class="acoes-linha">
                <a class="botao-pequeno"
                   href="<?= e(url('admin/produto_form.php?id=' . (int) $produto['id'])) ?>">Editar</a>

                <?php // Ligar/desligar sem precisar abrir o formulario ?>
                <form method="post" action="<?= e(url('admin/produto_excluir.php')) ?>">
                  <?= csrfCampo() ?>
                  <input type="hidden" name="acao" value="alternar">
                  <input type="hidden" name="id" value="<?= (int) $produto['id'] ?>">
                  <button type="submit" class="botao-pequeno">
                    <?= $produto['ativo'] ? 'Esconder' : 'Publicar' ?>
                  </button>
                </form>

                <form method="post" action="<?= e(url('admin/produto_excluir.php')) ?>"
                      onsubmit="return confirm('Excluir este produto? As fotos dele tambem serao apagadas.');">
                  <?= csrfCampo() ?>
                  <input type="hidden" name="acao" value="excluir">
                  <input type="hidden" name="id" value="<?= (int) $produto['id'] ?>">
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
    <h2>Nenhum produto encontrado</h2>
    <p>
      <?= ($busca !== '' || $categoriaId > 0)
            ? 'Tente outro filtro ou limpe a busca.'
            : 'Clique em "Novo produto" para cadastrar a primeira peca.' ?>
    </p>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
