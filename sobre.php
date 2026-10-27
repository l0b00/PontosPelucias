<?php
/**
 * sobre.php - Quem e a Carol e como funciona a encomenda
 * E uma pagina de texto: nao busca nada no banco.
 */

require __DIR__ . '/includes/conexao.php';
require __DIR__ . '/includes/funcoes.php';

$tituloPagina    = 'Sobre a Carol - ' . NOME_SITE;
$descricaoPagina = 'Conheca a Carol, como encomendar um amigurumi, prazo de producao, '
                 . 'pagamento por Pix e envio pelos Correios.';
require __DIR__ . '/includes/header.php';
?>

<div class="container">
  <p class="caminho">
    <a href="<?= e(url('index.php')) ?>">Inicio</a> &rsaquo; Sobre a Carol
  </p>

  <div class="cabecalho-pagina">
    <h1>Sobre a Carol</h1>
    <p>Croche feito ponto a ponto, uma peca de cada vez</p>
  </div>

  <div class="texto">
    <h2>Quem faz</h2>
    <p>
      A Carol e artesa e faz amigurumis &mdash; aqueles bichinhos e bonequinhos
      de croche &mdash; desde que aprendeu a tecnica em casa, por curiosidade.
      O que comecou como passatempo virou o atelie <strong>Ponntos &amp; Pelucias</strong>:
      chaveiros, personagens, bichinhos, pelucias grandes para abracar e
      pecas sob encomenda, todas feitas a mao, uma de cada vez.
    </p>
    <p>
      Por isso nenhuma peca sai exatamente igual a outra. Pequenas diferencas de
      cor, tamanho e acabamento fazem parte: e o jeito de saber que aquilo foi
      feito so para voce.
    </p>

    <h2>Como encomendar</h2>
    <p>
      Este site e um catalogo: nao tem carrinho nem pagamento online. O caminho e
      simples:
    </p>
    <ol>
      <li>Escolha a peca no catalogo e abra a pagina dela.</li>
      <li>Clique em <strong>Encomendar pelo WhatsApp</strong> e preencha seu nome,
          telefone, quantidade e o que mais quiser combinar (cores, nome bordado,
          data que precisa).</li>
      <li>O WhatsApp abre com a mensagem pronta. A Carol confirma o valor,
          o prazo e o frete.</li>
    </ol>

    <h2>Prazo de producao</h2>
    <p>
      Como tudo e feito a mao, o prazo varia com o tamanho da peca e com a fila
      de encomendas do momento. Em geral:
    </p>
    <ul>
      <li>Chaveiros e pecas pequenas: de 3 a 7 dias.</li>
      <li>Bonecos e bichinhos medios: de 7 a 15 dias.</li>
      <li>Pelucias grandes e kits: a combinar.</li>
    </ul>
    <p>
      Para datas especificas (aniversario, dia das maes, formatura), avise na
      hora da encomenda para a Carol conferir se da tempo.
    </p>

    <h2>Pagamento</h2>
    <p>
      Somente <strong>Pix</strong>, combinado direto com a Carol pelo WhatsApp.
      A chave Pix e <strong><?= e(PIX_CHAVE) ?></strong>. O pagamento confirma a
      encomenda e entra na fila de producao.
    </p>

    <h2>Entrega</h2>
    <p>
      O envio e feito pelos <strong>Correios</strong>, do atelie direto para a sua
      casa, com o codigo de rastreio enviado pelo WhatsApp assim que a peca e
      postada. Quem mora perto pode combinar a retirada.
    </p>

    <h2>Fale com a Carol</h2>
    <p>
      <a class="botao botao-whatsapp"
         href="<?= e(linkWhatsApp('Oi, Carol! Vim pelo site e queria encomendar uma peca.')) ?>"
         target="_blank" rel="noopener">Chamar no WhatsApp</a>
    </p>
    <p>
      No Instagram <a href="https://instagram.com/<?= e(INSTAGRAM) ?>" target="_blank"
      rel="noopener">@<?= e(INSTAGRAM) ?></a> da para ver as pecas que acabaram de sair
      do atelie.
    </p>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
