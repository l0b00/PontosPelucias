<?php
require_once __DIR__ . '/includes/conexao.php';

// Identifica a página atual
$pagina_atual = basename($_SERVER['PHP_SELF']);

// Busca as redes sociais cadastradas no banco
$sql = "SELECT * FROM contatos_redes
        WHERE ativo = 1
        ORDER BY ordem ASC";

$stmt = $pdo->query($sql);
$redes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Busca as configurações do rodapé
$sql = "SELECT * FROM configuracoes_rodape
        WHERE ativo = 1
        LIMIT 1";

$stmt = $pdo->query($sql);
$config = $stmt->fetch(PDO::FETCH_ASSOC);

// Localiza o WhatsApp
$whatsapp = null;

foreach ($redes as $rede) {
    if (strtolower($rede['nome']) === 'whatsapp') {
        $whatsapp = $rede;
        break;
    }
}
?>

<!-- 1. REDES SOCIAIS: EXCETO NA PÁGINA DE CONTATO -->
<?php if ($pagina_atual !== 'contato.php'): ?>

<section class="redes_loja">

    <h2>Mais fofuras por aqui!</h2>

    <p>Novidades, encomendas e minhas criações especiais.</p>

    <div class="redes_botoes">

        <?php foreach ($redes as $rede): ?>

            <?php if (strtolower($rede['nome']) !== 'whatsapp'): ?>

                <a href="<?= htmlspecialchars($rede['link'], ENT_QUOTES, 'UTF-8') ?>"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="rede_botao">

                    <img src="<?= htmlspecialchars($rede['icone'], ENT_QUOTES, 'UTF-8') ?>"
                         alt="<?= htmlspecialchars($rede['nome'], ENT_QUOTES, 'UTF-8') ?>">

                </a>

            <?php endif; ?>

        <?php endforeach; ?>

    </div>

</section>

<?php endif; ?>


<!-- 2. CHAMADA DO CATÁLOGO: EXCETO NO CATÁLOGO -->
<?php if ($pagina_atual !== 'catalogo.php'): ?>

<section class="catalogo_chamada">

    <h2>Encontrou sua nova fofura?</h2>

    <p>Conheça todas as pelúcias feitas à mão!</p>

    <a href="catalogo.php" class="catalogo_botao">
        Conhecer catálogo ♡
    </a>

</section>

<?php endif; ?>


<!-- 3. RODAPÉ PRINCIPAL: TODAS AS PÁGINAS -->
<footer class="rodape_loja">

    <h3>
        <?= htmlspecialchars($config['titulo'] ?? 'Feito à mão, com muito amor ♡', ENT_QUOTES, 'UTF-8') ?>
    </h3>

    <p>
        <?= htmlspecialchars($config['mensagem'] ?? 'Obrigada por apoiar meu trabalho artesanal!', ENT_QUOTES, 'UTF-8') ?>
    </p>

    <p>
        <?= htmlspecialchars($config['texto_final'] ?? '♡ Pontos e Pelúcias by Carol ♡', ENT_QUOTES, 'UTF-8') ?>
    </p>

</footer>


<!-- 4. WHATSAPP FLUTUANTE: EXCETO NA PÁGINA DE CONTATO -->
<?php if ($pagina_atual !== 'contato.php' && $whatsapp): ?>

<a href="<?= htmlspecialchars($whatsapp['link'], ENT_QUOTES, 'UTF-8') ?>"
   class="whatsapp_flutuante"
   target="_blank"
   rel="noopener noreferrer"
   aria-label="Conversar pelo WhatsApp">

    <img src="<?= htmlspecialchars($whatsapp['icone'], ENT_QUOTES, 'UTF-8') ?>"
         alt="WhatsApp">

</a>

<?php endif; ?>
