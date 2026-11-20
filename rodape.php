<?php
// Descobre qual arquivo está aberto no momento (ex: index.php, contato.php, catalogo.php)
$pagina_atual = basename($_SERVER['PHP_SELF']);
?>

<!-- 1. REDES SOCIAIS: Aparece em tudo, EXCETO na página contato.php -->
<?php if ($pagina_atual !== 'contato.php'): ?>
<section class="redes_loja">

    <h2>Mais fofuras por aqui!</h2>

    <p>Novidades, encomendas e minhas criações especiais.</p>

    <div class="redes_botoes">

        <a href="https://instagram.com"
           target="_blank"
           rel="noopener noreferrer"
           class="rede_botao">
            <img src="img/instagram.png" alt="Instagram">
        </a>

        <a href="https://tiktok.com"
           target="_blank"
           rel="noopener noreferrer"
           class="rede_botao">
            <img src="img/tiktok.png" alt="TikTok">
        </a>

    </div>

</section>
<?php endif; ?>


<!-- 2. CHAMADA DO CATÁLOGO: Aparece em tudo, EXCETO na página catalogo.php -->
<?php if ($pagina_atual !== 'catalogo.php'): ?>
<section class="catalogo_chamada">

    <h2>Encontrou sua nova fofura?</h2>

    <p>Conheça todas as pelúcias feitas à mão!</p>

    <a href="catalogo.php" class="catalogo_botao">
        Conhecer catálogo ♡
    </a>

</section>
<?php endif; ?>


<!-- 3. RODAPÉ PRINCIPAL: Aparece em TODAS as páginas -->
<footer class="rodape_loja">

    <h3>Feito à mão, com muito amor ♡</h3>

    <p>Obrigada por apoiar meu trabalho artesanal!</p>

</footer>


<!-- 4. WHATSAPP FLUTUANTE: Aparece em tudo, EXCETO na página contato.php -->
<?php if ($pagina_atual !== 'contato.php'): ?>
<a href="https://wa.me"
   class="whatsapp_flutuante"
   target="_blank"
   rel="noopener noreferrer"
   aria-label="Conversar pelo WhatsApp">

    <img src="img/whatsapp.png" alt="WhatsApp">

</a>
<?php endif; ?>
