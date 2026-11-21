
<?php
require_once 'includes/conexao.php';

$sql = "SELECT * FROM contatos_redes
        WHERE ativo = 1
        ORDER BY ordem ASC";

$stmt = $pdo->query($sql);
$redes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Contato - Pontos e Pelúcias</title>

    <link rel="stylesheet" href="style.css">

    <link href="https://fonts.googleapis.com/css2?family=DynaPuff:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap" rel="stylesheet">
</head>

<body>

    <header class="menu_principal">
        <div class="cabeçalho">

            <details class="menu">
                <summary class="menu_botao">
                    <span class="hamburguer">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                    <span class="menu_txt">Menu</span>
                </summary>

                <ul class="topico">
                    <li><a class="link" href="index.php">Início</a></li>
                    <li><a class="link" href="catalogo.php">Catálogo</a></li>
                    <li><a class="link" href="contato.php">Contato</a></li>
                </ul>
            </details>

            <nav class="pag_inicial">
                <h1>
                    <a class="link" href="index.php">
                        Pontos e Pelúcias
                    </a>
                </h1>
                <span class="subtitulo">by Carol</span>
            </nav>

        </div>
    </header>

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
