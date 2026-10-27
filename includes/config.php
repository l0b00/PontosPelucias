<?php
/**
 * config.php - Configuracoes do site
 *
 * Este e o UNICO arquivo que voce precisa editar para o site funcionar na sua
 * maquina ou para trocar os dados de contato da Carol. Nenhuma senha de cliente
 * fica aqui: so os dados do banco e os links da loja.
 */

// ---------------------------------------------------------------------------
// Banco de dados (padrao do XAMPP: usuario "root" e senha vazia)
// ---------------------------------------------------------------------------
define('BD_HOST', 'localhost');
define('BD_NOME', 'pelucias');
define('BD_USUARIO', 'root');
define('BD_SENHA', '');

// ---------------------------------------------------------------------------
// Endereco do site
// Se o projeto estiver em htdocs/pelucias, deixe '/pelucias'.
// Se estiver direto na raiz do htdocs, troque por '' (string vazia).
// ---------------------------------------------------------------------------
define('URL_BASE', '/pelucias');

// ---------------------------------------------------------------------------
// Contato da Carol
// WHATSAPP: so numeros, com codigo do pais (55) e DDD. Sem +, espaco ou traco.
// ---------------------------------------------------------------------------
define('WHATSAPP', '5511999999999');
define('INSTAGRAM', 'ponntosepelucias');
define('PIX_CHAVE', 'carol@email.com');

// ---------------------------------------------------------------------------
// Dados do trabalho (aparecem no rodape)
// ---------------------------------------------------------------------------
define('NOME_SITE', 'Ponntos & Pelucias by Carol');
define('EQUIPE', 'Lais, Aline, Vitor e Matheus');

// ---------------------------------------------------------------------------
// Regras de upload de imagem (usadas em funcoes.php e no admin)
// ---------------------------------------------------------------------------
define('UPLOAD_TAMANHO_MAX', 3 * 1024 * 1024); // 3 MB
define('PRODUTOS_POR_PAGINA', 12);

// ---------------------------------------------------------------------------
// Mostrar todos os erros enquanto o projeto esta em desenvolvimento.
// Ao publicar de verdade, troque display_errors para '0'.
// ---------------------------------------------------------------------------
error_reporting(E_ALL);
ini_set('display_errors', '1');

// ---------------------------------------------------------------------------
// Sessao: precisa ser configurada ANTES de session_start().
// httponly  = o JavaScript nao consegue ler o cookie (protege contra XSS)
// samesite  = o cookie nao e enviado em requisicoes vindas de outro site (CSRF)
// ---------------------------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// Fuso horario e formato de datas em portugues
date_default_timezone_set('America/Sao_Paulo');
