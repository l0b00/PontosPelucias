<?php
/**
 * login.php - Entrada do painel da Carol
 *
 * Pontos de seguranca desta tela:
 *   - a senha e conferida com password_verify() contra o hash do banco;
 *   - a mensagem de erro e sempre a mesma ("E-mail ou senha invalidos"), para
 *     nao contar a um invasor se o e-mail existe ou nao;
 *   - session_regenerate_id(true) troca o numero da sessao ao logar, o que
 *     impede o ataque de "fixacao de sessao".
 */

require_once __DIR__ . '/../includes/conexao.php';
require_once __DIR__ . '/../includes/funcoes.php';

// Quem ja esta logado nao precisa ver esta tela.
if (!empty($_SESSION['admin_id'])) {
    redirecionar(url('admin/index.php'));
}

$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();

    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $consulta = $pdo->prepare('SELECT id, nome, email, senha_hash FROM admins WHERE email = ?');
    $consulta->execute([$email]);
    $admin = $consulta->fetch();

    if ($admin && password_verify($senha, $admin['senha_hash'])) {
        // Login certo.
        session_regenerate_id(true);
        $_SESSION['admin_id']    = $admin['id'];
        $_SESSION['admin_nome']  = $admin['nome'];
        $_SESSION['admin_email'] = $admin['email'];

        definirFlash('sucesso', 'Bem-vinda, ' . $admin['nome'] . '!');
        redirecionar(url('admin/index.php'));
    }

    // Login errado: mensagem generica de proposito.
    definirFlash('erro', 'E-mail ou senha invalidos.');
}
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Entrar no painel - <?= e(NOME_SITE) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
<link rel="stylesheet" href="<?= e(url('admin/assets/admin.css')) ?>">
<link rel="icon" href="<?= e(url('assets/img/favicon.svg')) ?>" type="image/svg+xml">
</head>
<body class="tela-login">

<main class="caixa-login">
  <a class="logo" href="<?= e(url('index.php')) ?>">
    <span class="logo-nome">Ponntos &amp; Pel&uacute;cias</span>
    <span class="logo-by">by Carol</span>
  </a>

  <h1>Area da Carol</h1>
  <p class="login-ajuda">Entre para cuidar dos produtos, das fotos e dos pedidos.</p>

  <?php mostrarFlash(); ?>

  <form method="post" action="<?= e(url('admin/login.php')) ?>">
    <?= csrfCampo() ?>

    <div class="campo">
      <label for="email">E-mail</label>
      <input type="email" id="email" name="email" required autofocus
             value="<?= e($email) ?>" autocomplete="username">
    </div>

    <div class="campo">
      <label for="senha">Senha</label>
      <input type="password" id="senha" name="senha" required autocomplete="current-password">
    </div>

    <button type="submit" class="botao">Entrar</button>
  </form>

  <p class="login-voltar"><a href="<?= e(url('index.php')) ?>">&larr; Voltar para o site</a></p>
</main>

</body>
</html>
