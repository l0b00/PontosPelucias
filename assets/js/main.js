/* ===========================================================================
   Ponntos & Pelucias by Carol - JavaScript do site

   O site funciona quase todo com HTML, CSS e PHP. O JavaScript cuida apenas de:
     1. abrir e fechar o menu lateral
     2. girar os carrosseis (setas, bolinhas, troca automatica e arrastar)
     3. trocar a foto grande ao clicar numa miniatura (pagina do produto)
     4. mostrar o formulario de encomenda
     5. pre-visualizar as imagens escolhidas no upload (painel admin)

   Tudo aqui e opcional: se o JavaScript estiver desligado, o site continua
   navegavel (os links e formularios sao HTML comum).
   =========================================================================== */

document.addEventListener('DOMContentLoaded', function () {
  iniciarMenu();
  iniciarCarrosseis();
  iniciarGaleria();
  iniciarFormularioEncomenda();
  iniciarPreviaDeUpload();
});

/* --- 1. Menu lateral ---------------------------------------------------- */
function iniciarMenu() {
  var abrir = document.getElementById('abrirMenu');
  var fechar = document.getElementById('fecharMenu');
  var menu = document.getElementById('menuLateral');
  var fundo = document.getElementById('fundoMenu');

  if (!abrir || !menu || !fundo) {
    return;
  }

  function mostrar(aberto) {
    menu.classList.toggle('aberto', aberto);
    fundo.classList.toggle('aberto', aberto);
    abrir.setAttribute('aria-expanded', aberto ? 'true' : 'false');
  }

  abrir.addEventListener('click', function () { mostrar(true); });
  fundo.addEventListener('click', function () { mostrar(false); });
  if (fechar) {
    fechar.addEventListener('click', function () { mostrar(false); });
  }
  // Tecla Esc tambem fecha.
  document.addEventListener('keydown', function (evento) {
    if (evento.key === 'Escape') { mostrar(false); }
  });
}

/* --- 2. Carrosseis ------------------------------------------------------ */
/* Funciona para qualquer bloco com a classe .carrossel:
     <div class="carrossel" data-auto="5000"> ... </div>
   Se existir uma div.bolinhas logo depois, ela ganha os indicadores. */
function iniciarCarrosseis() {
  var carrosseis = document.querySelectorAll('.carrossel');

  carrosseis.forEach(function (carrossel) {
    var trilho = carrossel.querySelector('.carrossel-trilho');
    if (!trilho) { return; }

    var itens = trilho.children;
    var total = itens.length;
    if (total <= 1) {
      // Com uma imagem so, nao precisa de seta nem bolinha.
      carrossel.querySelectorAll('.carrossel-seta').forEach(function (s) {
        s.style.display = 'none';
      });
      return;
    }

    var atual = 0;

    // As bolinhas podem estar logo depois do carrossel ou em outro lugar da
    // pagina, indicadas pelo atributo data-bolinhas="#idDaDiv".
    var bolinhas = null;
    if (carrossel.dataset.bolinhas) {
      bolinhas = document.querySelector(carrossel.dataset.bolinhas);
    } else {
      var proximo = carrossel.nextElementSibling;
      if (proximo && proximo.classList.contains('bolinhas')) {
        bolinhas = proximo;
      }
    }

    // Cria uma bolinha para cada imagem.
    if (bolinhas) {
      for (var i = 0; i < total; i++) {
        var botao = document.createElement('button');
        botao.type = 'button';
        botao.setAttribute('aria-label', 'Ir para a imagem ' + (i + 1));
        botao.dataset.indice = i;
        botao.addEventListener('click', function () {
          irPara(Number(this.dataset.indice));
        });
        bolinhas.appendChild(botao);
      }
    }

    function irPara(indice) {
      atual = (indice + total) % total;
      trilho.style.transform = 'translateX(' + (-100 * atual) + '%)';
      if (bolinhas) {
        Array.prototype.forEach.call(bolinhas.children, function (b, i) {
          b.classList.toggle('ativa', i === atual);
        });
      }
    }

    carrossel.querySelectorAll('.carrossel-seta.anterior').forEach(function (seta) {
      seta.addEventListener('click', function () { irPara(atual - 1); reiniciarRelogio(); });
    });
    carrossel.querySelectorAll('.carrossel-seta.proxima').forEach(function (seta) {
      seta.addEventListener('click', function () { irPara(atual + 1); reiniciarRelogio(); });
    });

    // Troca sozinho de tempos em tempos (padrao: 5 segundos).
    var intervalo = Number(carrossel.dataset.auto || 0);
    var relogio = null;

    function reiniciarRelogio() {
      if (!intervalo) { return; }
      clearInterval(relogio);
      relogio = setInterval(function () { irPara(atual + 1); }, intervalo);
    }

    // Arrastar com o dedo no celular (swipe).
    var inicioX = 0;
    carrossel.addEventListener('touchstart', function (evento) {
      inicioX = evento.changedTouches[0].clientX;
    }, { passive: true });

    carrossel.addEventListener('touchend', function (evento) {
      var distancia = evento.changedTouches[0].clientX - inicioX;
      if (Math.abs(distancia) > 40) {
        irPara(distancia < 0 ? atual + 1 : atual - 1);
        reiniciarRelogio();
      }
    }, { passive: true });

    irPara(0);
    reiniciarRelogio();
  });
}

/* --- 3. Galeria da pagina do produto ------------------------------------ */
function iniciarGaleria() {
  var fotoGrande = document.getElementById('fotoPrincipal');
  var miniaturas = document.querySelectorAll('.miniaturas button');

  if (!fotoGrande || miniaturas.length === 0) { return; }

  miniaturas.forEach(function (botao) {
    botao.addEventListener('click', function () {
      fotoGrande.src = this.dataset.foto;
      fotoGrande.alt = this.dataset.alt || fotoGrande.alt;
      miniaturas.forEach(function (b) { b.classList.remove('ativa'); });
      this.classList.add('ativa');
    });
  });
}

/* --- 4. Formulario de encomenda ----------------------------------------- */
function iniciarFormularioEncomenda() {
  var botao = document.getElementById('abrirEncomenda');
  var formulario = document.getElementById('formEncomenda');

  if (!botao || !formulario) { return; }

  // Se o envio deu erro, o PHP marca data-manter-aberto e o formulario
  // continua visivel com o que a pessoa ja tinha digitado.
  if (formulario.dataset.manterAberto) { return; }

  // Sem JavaScript o formulario ja aparece aberto; aqui ele comeca fechado.
  formulario.hidden = true;
  botao.hidden = false;

  botao.addEventListener('click', function () {
    formulario.hidden = false;
    botao.hidden = true;
    var primeiro = formulario.querySelector('input, textarea');
    if (primeiro) { primeiro.focus(); }
    formulario.scrollIntoView({ behavior: 'smooth', block: 'center' });
  });
}

/* --- 5. Previa das imagens no upload (admin) ---------------------------- */
function iniciarPreviaDeUpload() {
  var campos = document.querySelectorAll('input[type="file"][data-previa]');

  campos.forEach(function (campo) {
    var destino = document.getElementById(campo.dataset.previa);
    if (!destino) { return; }

    campo.addEventListener('change', function () {
      destino.innerHTML = '';
      Array.prototype.forEach.call(this.files, function (arquivo) {
        if (!arquivo.type.startsWith('image/')) { return; }
        var img = document.createElement('img');
        img.src = URL.createObjectURL(arquivo);
        img.alt = 'Previa de ' + arquivo.name;
        img.className = 'previa-imagem';
        destino.appendChild(img);
      });
    });
  });
}
