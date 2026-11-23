
<?php
require_once 'includes/conexao.php';

$sql = "SELECT * FROM contatos_redes
        WHERE ativo = 1
        ORDER BY ordem ASC";

$stmt = $pdo->query($sql);
$redes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$titulo = 'Contato - Pontos e Pelúcias';
require 'includes/cabecalho.php';
?>

    <main class="contato_principal">

        <section class="contato_intro">
            <h2>Entre em contato!</h2>

            <p>
                Ficou com alguma dúvida ou gostou de algum produto?
                Entre em contato comigo ou acompanhe a loja pelas redes sociais!
            </p>
        </section>

        <section class="contato_cards">

            <?php foreach ($redes as $rede): ?>

                <div class="contato_card">

                    <div class="contato_icone">
                        <img
                            src="<?= htmlspecialchars($rede['icone']) ?>"
                            alt="<?= htmlspecialchars($rede['nome']) ?>"
                        >
                    </div>

                    <h3>
                        <?= htmlspecialchars($rede['nome']) ?>
                    </h3>

                    <p>
                        <?= htmlspecialchars($rede['descricao']) ?>
                    </p>

                    <a
                        class="contato_botao <?= htmlspecialchars($rede['classe_css']) ?>"
                        href="<?= htmlspecialchars($rede['link']) ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <?= htmlspecialchars($rede['texto_botao']) ?>
                    </a>

                </div>

            <?php endforeach; ?>

        </section>
    </main>
    
<?php include 'rodape.php';?>

</body>
</html>
