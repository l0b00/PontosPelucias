<?php
session_start();

// Inclua o seu arquivo de conexão com o banco de dados aqui (ex: include 'conexao.php';)
// Certifique-se de que a variável $pdo está configurada e funcionando.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (!empty($usuario) && !empty($senha)) {
        // Busca o administrador no banco de dados pelo e-mail
        $sql = "SELECT * FROM administradores WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuario]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se o admin existe e se a senha confere com o hash do banco
        if ($admin && password_verify($senha, $admin['senha'])) {
            $_SESSION['is_admin'] = true;
            $_SESSION['admin_email'] = $admin['email'];
            
            // Redireciona para o painel administrativo correto
            header('Location: index_admin.php');
            exit;
        } else {
            $erro = "Usuário ou senha incorretos!";
        }
    } else {
        $erro = "Por favor, preencha todos os campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>administrador</title>
    <link rel="stylesheet" href="style.css">

    <link href="https://fonts.googleapis.com/css2?family=DynaPuff:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="login-container">
        <h2 class="titulo_login">Login</h2>

        <!-- Exibe mensagem de erro caso exista -->
        <?php if (!empty($erro)): ?>
            <div><?php echo $erro; ?></div>
        <?php endif; ?>

        <form class="login" action="login_admin.php" method="POST">
            <div class="usuario">
                <label class="usuario_txt" for="usuario">Usuário ou E-mail:</label><br>
                <input class="usuario_box" type="text" id="usuario" name="usuario" required>
            </div>
            
            <br>

            <div class="senha">
                <label class="senha_txt" for="senha">Senha:</label><br>
                <input class="senha_box" type="password" id="senha" name="senha" required>
            </div>

            <br>

            <button class="entrar" type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>