<?php
session_start();

// Exemplo simples de validação do formulário de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Substitua pela sua validação real no banco de dados
    if ($usuario === 'admin' && $senha === '1234') {
        // Define que o usuário está logado e é admin
        $_SESSION['is_admin'] = true;
        
        // Redireciona para o painel administrativo
        header('Location: admin.php');
        exit;
    } else {
        $erro = "Usuário ou senha incorretos!";
    }
}
?>

<!-- Formulário de Login (login.php) -->
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Área de Login</h2>
    <?php if (isset($erro)) echo "<p style='color:red;'>$erro</p>"; ?>
    <form method="POST" action="login.php">
        <input type="text" name="usuario" placeholder="Usuário" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <button type="submit">Entrar</button>
    </form>
</body>
</html>