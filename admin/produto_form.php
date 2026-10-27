<?php
/**
 * admin/produto_form.php - Cria e edita produto (o mesmo formulario serve aos dois)
 *
 * Se vier ?id=5 na URL, estamos editando; sem id, estamos criando.
 * O mesmo arquivo tambem recebe o POST e grava no banco.
 */

require __DIR__ . '/proteger.php';

$id = (int) ($_GET['id'] ?? 0);
$editando = $id > 0;

// Valores que aparecem no formulario. Comecam vazios (produto novo).
$produto = [
    'nome' => '', 'categoria_id' => '', 'descricao' => '', 'materiais' => '',
    'tamanho' => '', 'preco' => '', 'mais_vendido' => 0, 'ativo' => 1,
];
$colecoesDoProduto = [];
$fotos = [];

if ($editando) {
    $consulta = $pdo->prepare('SELECT * FROM produtos WHERE id = ?');
    $consulta->execute([$id]);
    $encontrado = $consulta->fetch();

    if (!$encontrado) {
        definirFlash('erro', 'Produto nao encontrado.');
        redirecionar(url('admin/produtos.php'));
    }
    $produto = $encontrado;

    $consulta = $pdo->prepare('SELECT colecao_id FROM colecao_produto WHERE produto_id = ?');
    $consulta->execute([$id]);
    $colecoesDoProduto = $consulta->fetchAll(PDO::FETCH_COLUMN);

    $consulta = $pdo->prepare(
        'SELECT id, arquivo, principal FROM produto_fotos
          WHERE produto_id = ? ORDER BY principal DESC, ordem ASC, id ASC'
    );
    $consulta->execute([$id]);
    $fotos = $consulta->fetchAll();
}

// ---------------------------------------------------------------------------
// GRAVAR (POST)
// ---------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();

    // --- 1. Le e valida os campos no servidor ---
    $nome        = trim($_POST['nome'] ?? '');
    $categoriaId = (int) ($_POST['categoria_id'] ?? 0);
    $descricao   = trim($_POST['descricao'] ?? '');
    $materiais   = trim($_POST['materiais'] ?? '');
    $tamanho     = trim($_POST['tamanho'] ?? '');
    $precoTexto  = (string) ($_POST['preco'] ?? '');
    $maisVendido = isset($_POST['mais_vendido']) ? 1 : 0;
    $ativo       = isset($_POST['ativo']) ? 1 : 0;
    $colecoes    = array_map('intval', $_POST['colecoes'] ?? []);

    $erros = [];

    if (mb_strlen($nome, 'UTF-8') < 2) {
        $erros[] = 'Escreva o nome do produto.';
    }
    if (mb_strlen($nome, 'UTF-8') > 150) {
        $erros[] = 'O nome passou de 150 caracteres.';
    }

    $preco = precoParaNumero($precoTexto);
    if ($preco === null) {
        $erros[] = 'Preco invalido. Escreva assim: 45,00';
    }

    // Categoria e opcional, mas se foi escolhida precisa existir mesmo.
    if ($categoriaId > 0) {
        $confere = $pdo->prepare('SELECT id FROM categorias WHERE id = ?');
        $confere->execute([$categoriaId]);
        if (!$confere->fetch()) {
            $erros[] = 'Categoria invalida.';
        }
    }

    if ($erros) {
        definirFlash('erro', implode(' ', $erros));
        // Devolve o que foi digitado para a pessoa nao perder o trabalho.
        $produto = array_merge($produto, [
            'nome' => $nome, 'categoria_id' => $categoriaId, 'descricao' => $descricao,
            'materiais' => $materiais, 'tamanho' => $tamanho, 'preco' => $precoTexto,
            'mais_vendido' => $maisVendido, 'ativo' => $ativo,
        ]);
        $colecoesDoProduto = $colecoes;
    } else {
        // --- 2. Grava o produto ---
        $slug = slugUnico($pdo, 'produtos', gerarSlug($nome), $editando ? $id : null);

        if ($editando) {
            $consulta = $pdo->prepare(
                'UPDATE produtos
                    SET categoria_id = ?, nome = ?, slug = ?, descricao = ?, materiais = ?,
                        tamanho = ?, preco = ?, mais_vendido = ?, ativo = ?
                  WHERE id = ?'
            );
            $consulta->execute([
                $categoriaId > 0 ? $categoriaId : null, $nome, $slug, $descricao, $materiais,
                $tamanho, $preco, $maisVendido, $ativo, $id,
            ]);
        } else {
            $consulta = $pdo->prepare(
                'INSERT INTO produtos
                   (categoria_id, nome, slug, descricao, materiais, tamanho, preco, mais_vendido, ativo)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $consulta->execute([
                $categoriaId > 0 ? $categoriaId : null, $nome, $slug, $descricao, $materiais,
                $tamanho, $preco, $maisVendido, $ativo,
            ]);
            $id = (int) $pdo->lastInsertId();
            $editando = true;
        }

        // --- 3. Regrava as colecoes (apaga as antigas e insere as marcadas) ---
        $pdo->prepare('DELETE FROM colecao_produto WHERE produto_id = ?')->execute([$id]);
        if ($colecoes) {
            $insere = $pdo->prepare(
                'INSERT INTO colecao_produto (colecao_id, produto_id) VALUES (?, ?)'
            );
            foreach ($colecoes as $colecaoId) {
                $insere->execute([$colecaoId, $id]);
            }
        }

        // --- 4. Fotos novas ---
        $enviadas = 0;
        $errosDeFoto = [];

        if (!empty($_FILES['fotos']['name'][0])) {
            $quantidade = count($_FILES['fotos']['name']);

            for ($i = 0; $i < $quantidade; $i++) {
                // $_FILES com varios arquivos vem "ao contrario": um array por
                // propriedade. Aqui remontamos um arquivo de cada vez.
                $arquivo = [
                    'name'     => $_FILES['fotos']['name'][$i],
                    'type'     => $_FILES['fotos']['type'][$i],
                    'tmp_name' => $_FILES['fotos']['tmp_name'][$i],
                    'error'    => $_FILES['fotos']['error'][$i],
                    'size'     => $_FILES['fotos']['size'][$i],
                ];
                if ($arquivo['error'] === UPLOAD_ERR_NO_FILE) {
                    continue;
                }

                $erroUpload = null;
                $nomeArquivo = salvarImagem($arquivo, 'produtos', $erroUpload);

                if ($nomeArquivo === null) {
                    $errosDeFoto[] = $arquivo['name'] . ': ' . $erroUpload;
                    continue;
                }

                // A primeira foto do produto ja entra como principal.
                $jaTem = $pdo->prepare('SELECT COUNT(*) FROM produto_fotos WHERE produto_id = ?');
                $jaTem->execute([$id]);
                $principal = ((int) $jaTem->fetchColumn() === 0) ? 1 : 0;

                $pdo->prepare(
                    'INSERT INTO produto_fotos (produto_id, arquivo, principal, ordem)
                     VALUES (?, ?, ?, ?)'
                )->execute([$id, $nomeArquivo, $principal, $i + 1]);

                $enviadas++;
            }
        }

        // --- 5. Foto principal escolhida nos radios ---
        $fotoPrincipal = (int) ($_POST['foto_principal'] ?? 0);
        if ($fotoPrincipal > 0) {
            // Confere que a foto e mesmo deste produto antes de mexer.
            $confere = $pdo->prepare('SELECT id FROM produto_fotos WHERE id = ? AND produto_id = ?');
            $confere->execute([$fotoPrincipal, $id]);
            if ($confere->fetch()) {
                $pdo->prepare('UPDATE produto_fotos SET principal = 0 WHERE produto_id = ?')
                    ->execute([$id]);
                $pdo->prepare('UPDATE produto_fotos SET principal = 1 WHERE id = ?')
                    ->execute([$fotoPrincipal]);
            }
        }

        $mensagem = $enviadas > 0
            ? "Produto salvo e {$enviadas} foto(s) enviada(s)."
            : 'Produto salvo.';
        if ($errosDeFoto) {
            $mensagem .= ' Algumas fotos nao entraram: ' . implode(' | ', $errosDeFoto);
        }

        definirFlash($errosDeFoto ? 'erro' : 'sucesso', $mensagem);
        redirecionar(url('admin/produto_form.php?id=' . $id));
    }
}

// --- Listas dos selects / checkboxes --------------------------------------
$categorias = $pdo->query('SELECT id, nome FROM categorias ORDER BY nome ASC')->fetchAll();
$colecoesTodas = $pdo->query('SELECT id, nome FROM colecoes ORDER BY nome ASC')->fetchAll();

$tituloAdmin = $editando ? 'Editar produto' : 'Novo produto';
$paginaAtiva = 'produtos';
require __DIR__ . '/includes/header.php';
?>

<p><a class="botao-pequeno" href="<?= e(url('admin/produtos.php')) ?>">&larr; Voltar para a lista</a></p>

<!-- enctype="multipart/form-data" e obrigatorio para o upload funcionar -->
<form method="post" enctype="multipart/form-data"
      action="<?= e(url('admin/produto_form.php' . ($editando ? '?id=' . $id : ''))) ?>">
  <?= csrfCampo() ?>

  <div class="cartao">
    <h2>Dados da peca</h2>

    <div class="campo">
      <label for="nome">Nome *</label>
      <input type="text" id="nome" name="nome" maxlength="150" required
             value="<?= e($produto['nome']) ?>">
    </div>

    <div class="linha-campos">
      <div class="campo">
        <label for="categoria_id">Categoria</label>
        <select id="categoria_id" name="categoria_id">
          <option value="0">Sem categoria</option>
          <?php foreach ($categorias as $categoria): ?>
            <option value="<?= (int) $categoria['id'] ?>"
              <?= (int) $produto['categoria_id'] === (int) $categoria['id'] ? 'selected' : '' ?>>
              <?= e($categoria['nome']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="campo">
        <label for="preco">Preco *</label>
        <input type="text" id="preco" name="preco" required inputmode="decimal"
               placeholder="45,00"
               value="<?= e(is_numeric($produto['preco']) ? number_format((float) $produto['preco'], 2, ',', '') : $produto['preco']) ?>">
        <small>Pode escrever com virgula: 45,00</small>
      </div>
    </div>

    <div class="campo">
      <label for="descricao">Descricao</label>
      <textarea id="descricao" name="descricao" maxlength="2000"><?= e($produto['descricao']) ?></textarea>
    </div>

    <div class="linha-campos">
      <div class="campo">
        <label for="materiais">Materiais</label>
        <input type="text" id="materiais" name="materiais" maxlength="255"
               placeholder="Fio veludo, enchimento de fibra..."
               value="<?= e($produto['materiais']) ?>">
      </div>

      <div class="campo">
        <label for="tamanho">Tamanho aproximado</label>
        <input type="text" id="tamanho" name="tamanho" maxlength="100"
               placeholder="Cerca de 10 cm"
               value="<?= e($produto['tamanho']) ?>">
      </div>
    </div>

    <div class="campo caixa-checkbox">
      <label>
        <input type="checkbox" name="mais_vendido" value="1"
          <?= $produto['mais_vendido'] ? 'checked' : '' ?>>
        Mostrar em &ldquo;Mais vendidos&rdquo;
      </label>
      <label>
        <input type="checkbox" name="ativo" value="1" <?= $produto['ativo'] ? 'checked' : '' ?>>
        Publicado no site
      </label>
    </div>
  </div>

  <div class="cartao">
    <h2>Colecoes</h2>
    <?php if ($colecoesTodas): ?>
      <div class="caixa-checkbox">
        <?php foreach ($colecoesTodas as $colecao): ?>
          <label>
            <input type="checkbox" name="colecoes[]" value="<?= (int) $colecao['id'] ?>"
              <?= in_array((int) $colecao['id'], array_map('intval', $colecoesDoProduto), true) ? 'checked' : '' ?>>
            <?= e($colecao['nome']) ?>
          </label>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p>Nenhuma colecao cadastrada ainda.</p>
    <?php endif; ?>
  </div>

  <div class="cartao">
    <h2>Fotos</h2>

    <?php if ($fotos): ?>
      <p>Escolha qual e a foto principal (a que aparece nos cards do site):</p>
      <div class="fotos-salvas">
        <?php foreach ($fotos as $foto): ?>
          <div class="foto-salva">
            <img src="<?= e(urlUpload('produtos', $foto['arquivo'])) ?>"
                 alt="Foto do produto" loading="lazy">
            <label>
              <input type="radio" name="foto_principal" value="<?= (int) $foto['id'] ?>"
                <?= $foto['principal'] ? 'checked' : '' ?>>
              principal
            </label>
          </div>
        <?php endforeach; ?>
      </div>
      <p style="font-size:.85rem;color:var(--texto-suave)">
        Para apagar uma foto, use os botoes abaixo da lista depois de salvar.
      </p>
    <?php elseif ($editando): ?>
      <p>Este produto ainda nao tem fotos.</p>
    <?php else: ?>
      <p>Voce pode enviar as fotos agora ou depois de salvar.</p>
    <?php endif; ?>

    <div class="campo">
      <label for="fotos">Enviar novas fotos</label>
      <input type="file" id="fotos" name="fotos[]" accept="image/jpeg,image/png,image/webp"
             multiple data-previa="previaFotos">
      <small>JPG, PNG ou WEBP, ate 3 MB cada. Da para escolher varias de uma vez.</small>
      <div class="previa" id="previaFotos"></div>
    </div>
  </div>

  <div class="botoes-formulario">
    <button type="submit" class="botao">Salvar produto</button>
    <a class="botao botao-secundario" href="<?= e(url('admin/produtos.php')) ?>">Cancelar</a>
  </div>
</form>

<?php if ($fotos): ?>
  <div class="cartao">
    <h2>Apagar fotos</h2>
    <p>Apagar uma foto remove o arquivo do servidor e nao tem como desfazer.</p>
    <div class="fotos-salvas">
      <?php foreach ($fotos as $foto): ?>
        <div class="foto-salva">
          <img src="<?= e(urlUpload('produtos', $foto['arquivo'])) ?>"
               alt="Foto do produto" loading="lazy">
          <form method="post" action="<?= e(url('admin/produto_foto.php')) ?>"
                onsubmit="return confirm('Apagar esta foto?');">
            <?= csrfCampo() ?>
            <input type="hidden" name="foto_id" value="<?= (int) $foto['id'] ?>">
            <input type="hidden" name="produto_id" value="<?= (int) $id ?>">
            <button type="submit" class="botao-pequeno perigo">Apagar</button>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
