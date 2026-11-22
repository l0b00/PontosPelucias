<?php
// Inicia a sessão para poder manipulá-la
session_start();

// Limpa todas as variáveis de sessão armazenadas
$_SESSION = array();

// Se for necessário destruir o cookie da sessão completamente
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destrói a sessão no servidor
session_destroy();

// Redireciona o usuário de volta para a tela de login
header('Location: login_admin_certo.php');
exit;
?>