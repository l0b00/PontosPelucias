<?php
/**
 * proteger.php - O porteiro do painel
 *
 * TODA pagina de dentro de admin/ (menos login.php) comeca com:
 *     require __DIR__ . '/proteger.php';
 *
 * Se a pessoa nao estiver logada, ela e mandada para a tela de login e o
 * resto da pagina nem chega a rodar.
 */

require_once __DIR__ . '/../includes/conexao.php';
require_once __DIR__ . '/../includes/funcoes.php';

if (empty($_SESSION['admin_id'])) {
    definirFlash('erro', 'Faca login para entrar no painel.');
    redirecionar(url('admin/login.php'));
}

// Dados do admin logado, usados no topo do painel.
$adminNome  = $_SESSION['admin_nome']  ?? 'Carol';
$adminEmail = $_SESSION['admin_email'] ?? '';
