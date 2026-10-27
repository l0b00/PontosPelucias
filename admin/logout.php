<?php
/**
 * logout.php - Sai do painel
 * Apaga tudo o que estava guardado na sessao e remove o cookie do navegador.
 */

require_once __DIR__ . '/../includes/conexao.php';
require_once __DIR__ . '/../includes/funcoes.php';

// 1. Esvazia os dados da sessao
$_SESSION = [];

// 2. Manda o navegador jogar fora o cookie da sessao
if (ini_get('session.use_cookies')) {
    $parametros = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $parametros['path'],
        $parametros['domain'],
        $parametros['secure'],
        $parametros['httponly']
    );
}

// 3. Destroi a sessao no servidor
session_destroy();

// 4. Comeca uma sessao nova so para conseguir mostrar a mensagem de despedida
session_start();
definirFlash('sucesso', 'Voce saiu do painel.');

redirecionar(url('admin/login.php'));
