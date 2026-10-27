<?php
/**
 * funcoes.php - Funcoes de apoio usadas pelo site inteiro
 *
 * Tudo que se repete em mais de uma pagina mora aqui: escapar texto, formatar
 * preco, gerar slug, token CSRF, mensagens de aviso e upload de imagem.
 * Nenhuma funcao daqui imprime HTML sozinha, exceto as marcadas com "imprime".
 */

require_once __DIR__ . '/config.php';

// Pasta fisica onde as imagens enviadas ficam gravadas.
define('PASTA_UPLOADS', dirname(__DIR__) . '/uploads');

// ---------------------------------------------------------------------------
// TEXTO E URL
// ---------------------------------------------------------------------------

/**
 * Escapa texto antes de jogar no HTML. Protege contra XSS.
 * TODA saida vinda do banco ou do usuario deve passar por aqui:
 *   <h1><?= e($produto['nome']) ?></h1>
 */
function e(?string $texto): string
{
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Monta um endereco a partir da raiz do site.
 *   url('produto.php?id=3')  ->  /pelucias/produto.php?id=3
 */
function url(string $caminho = ''): string
{
    return URL_BASE . '/' . ltrim($caminho, '/');
}

/** Endereco publico de uma imagem enviada. */
function urlUpload(string $subpasta, ?string $arquivo): string
{
    if ($arquivo === null || $arquivo === '') {
        return url('assets/img/sem-foto.svg');
    }
    return url('uploads/' . $subpasta . '/' . rawurlencode($arquivo));
}

/**
 * Usado nos banners: a Carol pode digitar um endereco de dentro do site
 * ("produto.php?id=1") ou um link completo ("https://instagram.com/...").
 */
function urlOuExterna(?string $link): string
{
    $link = trim((string) $link);
    if ($link === '') {
        return '#';
    }

    return preg_match('#^https?://#i', $link) ? $link : url($link);
}

/** Redireciona o navegador e encerra o script. */
function redirecionar(string $destino): void
{
    header('Location: ' . $destino);
    exit;
}

/**
 * Transforma "Pinguins beijoqueiros!" em "pinguins-beijoqueiros".
 * Usado para montar as URLs bonitas de categoria e colecao.
 */
function gerarSlug(string $texto): string
{
    // Troca os acentos pelas letras simples correspondentes.
    $com = ['á','à','â','ã','ä','é','è','ê','ë','í','ì','î','ï',
            'ó','ò','ô','õ','ö','ú','ù','û','ü','ç','ñ'];
    $sem = ['a','a','a','a','a','e','e','e','e','i','i','i','i',
            'o','o','o','o','o','u','u','u','u','c','n'];
    $texto = str_replace($com, $sem, mb_strtolower(trim($texto), 'UTF-8'));

    // O que nao for letra ou numero vira hifen.
    $texto = preg_replace('/[^a-z0-9]+/', '-', $texto);

    return trim($texto, '-');
}

/**
 * Garante que o slug nao se repita na tabela. Se "gatinho" ja existir,
 * devolve "gatinho-2", "gatinho-3" e assim por diante.
 */
function slugUnico(PDO $pdo, string $tabela, string $slug, ?int $idAtual = null): string
{
    // $tabela nunca vem do usuario: so pode ser um destes tres nomes.
    if (!in_array($tabela, ['produtos', 'categorias', 'colecoes'], true)) {
        throw new InvalidArgumentException('Tabela invalida para slug.');
    }

    $base = $slug !== '' ? $slug : 'item';
    $slug = $base;
    $n = 1;

    while (true) {
        $sql = "SELECT id FROM {$tabela} WHERE slug = ?";
        $parametros = [$slug];
        if ($idAtual !== null) {
            $sql .= ' AND id <> ?';
            $parametros[] = $idAtual;
        }
        $consulta = $pdo->prepare($sql);
        $consulta->execute($parametros);

        if ($consulta->fetch() === false) {
            return $slug;
        }
        $n++;
        $slug = $base . '-' . $n;
    }
}

// ---------------------------------------------------------------------------
// DINHEIRO
// ---------------------------------------------------------------------------

/** 60 -> "R$ 60,00" */
function formatarPreco($valor): string
{
    return 'R$ ' . number_format((float) $valor, 2, ',', '.');
}

/**
 * Converte o que a Carol digitou no formulario para um numero do banco.
 * Aceita "45,00", "45.00", "1.250,90" e "R$ 45".
 * Devolve null quando nao e um valor valido.
 */
function precoParaNumero(string $texto): ?float
{
    $texto = trim(str_replace(['R$', ' ', "\xc2\xa0"], '', $texto));
    if ($texto === '') {
        return null;
    }
    // Se tem virgula, ela e a casa decimal e o ponto e separador de milhar.
    if (strpos($texto, ',') !== false) {
        $texto = str_replace('.', '', $texto);
        $texto = str_replace(',', '.', $texto);
    }
    if (!is_numeric($texto)) {
        return null;
    }
    $valor = (float) $texto;

    return $valor >= 0 ? round($valor, 2) : null;
}

/** Deixa so os numeros de um telefone: "(11) 98888-7777" -> "11988887777" */
function somenteNumeros(string $texto): string
{
    return preg_replace('/\D/', '', $texto);
}

/** Monta o link do WhatsApp da Carol ja com a mensagem escrita. */
function linkWhatsApp(string $mensagem = ''): string
{
    $base = 'https://wa.me/' . WHATSAPP;

    return $mensagem === '' ? $base : $base . '?text=' . rawurlencode($mensagem);
}

/** Monta o link para a Carol falar com um cliente que fez pedido. */
function linkWhatsAppCliente(string $telefone, string $mensagem = ''): string
{
    $numero = somenteNumeros($telefone);
    // Se a pessoa digitou so DDD + numero, acrescenta o 55 do Brasil.
    if (strlen($numero) <= 11) {
        $numero = '55' . $numero;
    }
    $base = 'https://wa.me/' . $numero;

    return $mensagem === '' ? $base : $base . '?text=' . rawurlencode($mensagem);
}

// ---------------------------------------------------------------------------
// CSRF - impede que outro site envie formularios no nosso lugar
// ---------------------------------------------------------------------------

/** Devolve o token da sessao, criando um na primeira vez. */
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/** Imprime o campo escondido. Vai dentro de TODO formulario POST. */
function csrfCampo(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

/** Confere o token recebido. hash_equals compara sem dar pista pelo tempo. */
function csrfValido(): bool
{
    $enviado = $_POST['csrf_token'] ?? '';

    return is_string($enviado)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $enviado);
}

/** Barra a pagina inteira quando o token nao confere. */
function exigirCsrf(): void
{
    if (!csrfValido()) {
        http_response_code(403);
        exit('Sessao expirada ou formulario invalido. Volte e tente de novo.');
    }
}

// ---------------------------------------------------------------------------
// MENSAGENS (flash): guardadas na sessao e mostradas na proxima pagina
// ---------------------------------------------------------------------------

/** $tipo pode ser 'sucesso' ou 'erro'. */
function definirFlash(string $tipo, string $mensagem): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensagem' => $mensagem];
}

/** Imprime a mensagem guardada (e apaga, para nao aparecer duas vezes). */
function mostrarFlash(): void
{
    if (empty($_SESSION['flash'])) {
        return;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    $classe = $flash['tipo'] === 'erro' ? 'aviso aviso-erro' : 'aviso aviso-sucesso';
    echo '<p class="' . $classe . '" role="status">' . e($flash['mensagem']) . '</p>';
}

// ---------------------------------------------------------------------------
// UPLOAD DE IMAGEM
// ---------------------------------------------------------------------------

/**
 * Grava uma imagem enviada pelo formulario dentro de uploads/<subpasta>.
 *
 * @param array  $arquivo  um item de $_FILES (ex.: $_FILES['imagem'])
 * @param string $subpasta produtos | banners | colecoes | categorias
 * @param string $erro     recebe a mensagem quando algo da errado
 * @return string|null     o novo nome do arquivo, ou null se falhou
 */
function salvarImagem(array $arquivo, string $subpasta, ?string &$erro = null): ?string
{
    $permitidas = ['produtos', 'banners', 'colecoes', 'categorias'];
    if (!in_array($subpasta, $permitidas, true)) {
        $erro = 'Pasta de upload invalida.';
        return null;
    }

    if (!isset($arquivo['error']) || $arquivo['error'] === UPLOAD_ERR_NO_FILE) {
        $erro = 'Nenhum arquivo enviado.';
        return null;
    }
    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        $erro = 'Falha no envio do arquivo (codigo ' . (int) $arquivo['error'] . ').';
        return null;
    }
    if ($arquivo['size'] > UPLOAD_TAMANHO_MAX) {
        $erro = 'A imagem passa de 3 MB. Diminua o tamanho e tente de novo.';
        return null;
    }
    // is_uploaded_file garante que o caminho veio mesmo de um upload HTTP.
    if (!is_uploaded_file($arquivo['tmp_name'])) {
        $erro = 'Arquivo invalido.';
        return null;
    }

    // Olha o conteudo de verdade do arquivo, nao a extensao do nome.
    // Assim um virus.php renomeado para foto.jpg e recusado aqui.
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $tipo  = $finfo->file($arquivo['tmp_name']);

    $extensoes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];
    if (!isset($extensoes[$tipo])) {
        $erro = 'Formato nao aceito. Envie JPG, PNG ou WEBP.';
        return null;
    }

    // Nome aleatorio: evita sobrescrever arquivo de outra pessoa e evita que
    // alguem adivinhe o endereco de uma imagem.
    $nome = bin2hex(random_bytes(16)) . '.' . $extensoes[$tipo];
    $pasta = PASTA_UPLOADS . '/' . $subpasta;

    if (!is_dir($pasta) && !mkdir($pasta, 0775, true) && !is_dir($pasta)) {
        $erro = 'Nao foi possivel criar a pasta de upload.';
        return null;
    }
    if (!move_uploaded_file($arquivo['tmp_name'], $pasta . '/' . $nome)) {
        $erro = 'Nao foi possivel gravar a imagem.';
        return null;
    }

    return $nome;
}

/** Apaga do disco a imagem que acabou de sair do banco. */
function apagarImagem(string $subpasta, ?string $arquivo): void
{
    if ($arquivo === null || $arquivo === '') {
        return;
    }
    // basename impede um nome tipo "../../index.php" apagar outro arquivo.
    $caminho = PASTA_UPLOADS . '/' . $subpasta . '/' . basename($arquivo);
    if (is_file($caminho)) {
        unlink($caminho);
    }
}

/** Traduz o codigo de erro do PHP quando o arquivo passa do limite do servidor. */
function houveEnvioDeArquivo(array $arquivo): bool
{
    return isset($arquivo['error']) && $arquivo['error'] !== UPLOAD_ERR_NO_FILE;
}

// ---------------------------------------------------------------------------
// LISTAGENS: ordenacao e paginacao (usadas em categoria, colecao e busca)
// ---------------------------------------------------------------------------

/**
 * Converte o ?ordem= da URL num trecho de SQL seguro.
 * Nunca colocamos texto do usuario direto no ORDER BY: so escolhemos
 * uma das opcoes desta lista.
 */
function ordemSql(?string $chave): string
{
    $opcoes = [
        'recentes'     => 'p.criado_em DESC',
        'menor_preco'  => 'p.preco ASC',
        'maior_preco'  => 'p.preco DESC',
        'nome'         => 'p.nome ASC',
    ];

    return $opcoes[$chave] ?? $opcoes['recentes'];
}

/** As opcoes que aparecem no <select> de ordenacao. */
function opcoesDeOrdem(): array
{
    return [
        'recentes'    => 'Mais recentes',
        'menor_preco' => 'Menor preco',
        'maior_preco' => 'Maior preco',
        'nome'        => 'Nome (A-Z)',
    ];
}

/** Numero da pagina atual, vindo do ?pagina= (sempre 1 ou mais). */
function paginaAtual(): int
{
    $pagina = (int) ($_GET['pagina'] ?? 1);

    return $pagina > 0 ? $pagina : 1;
}

/**
 * Imprime a barra "1 2 3 >" mantendo os filtros que ja estao na URL.
 *
 * @param array $parametros o que deve continuar na URL (ex.: ['slug' => 'animais'])
 */
function imprimirPaginacao(string $arquivo, array $parametros, int $pagina, int $totalPaginas): void
{
    if ($totalPaginas <= 1) {
        return;
    }
    echo '<nav class="paginacao" aria-label="Paginas de produtos">';

    for ($i = 1; $i <= $totalPaginas; $i++) {
        $parametros['pagina'] = $i;
        $endereco = url($arquivo) . '?' . http_build_query($parametros);

        if ($i === $pagina) {
            echo '<span class="paginacao-atual" aria-current="page">' . $i . '</span>';
        } else {
            echo '<a href="' . e($endereco) . '">' . $i . '</a>';
        }
    }
    echo '</nav>';
}

// ---------------------------------------------------------------------------
// CONSULTAS REPETIDAS
// ---------------------------------------------------------------------------

/**
 * Monta o SELECT de card de produto (foto principal junto), para nao repetir
 * o mesmo JOIN em categoria.php, colecao.php, busca.php e index.php.
 */
function selectDeCards(): string
{
    return 'SELECT p.id, p.nome, p.slug, p.preco, p.mais_vendido,
                   (SELECT f.arquivo FROM produto_fotos f
                     WHERE f.produto_id = p.id
                     ORDER BY f.principal DESC, f.ordem ASC, f.id ASC
                     LIMIT 1) AS foto
              FROM produtos p';
}

/** Lista as categorias ativas, em ordem - usada no menu e na home. */
function categoriasAtivas(PDO $pdo): array
{
    return $pdo->query(
        'SELECT id, nome, slug, imagem FROM categorias
          WHERE ativo = 1 ORDER BY ordem ASC, nome ASC'
    )->fetchAll();
}

/** Nomes amigaveis dos status de pedido (usados no admin). */
function statusDePedido(): array
{
    return [
        'novo'        => 'Novo',
        'em_producao' => 'Em producao',
        'pago'        => 'Pago',
        'enviado'     => 'Enviado',
        'entregue'    => 'Entregue',
        'cancelado'   => 'Cancelado',
    ];
}
