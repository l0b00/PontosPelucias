# Ponntos & Pelúcias by Carol

Site-catálogo de amigurumis e pelúcias de crochê feitos à mão pela artesã Carol.
Projeto integrador do 2º semestre — **Laís, Aline, Vitor e Matheus**.

## Subir o site

Precisa do **XAMPP** instalado (traz o Apache e o MariaDB/MySQL). No **PowerShell**,
dentro da pasta do projeto:

```powershell
# 1. Deixar o projeto visível para o Apache (pule se já clonou dentro de htdocs)
New-Item -ItemType Junction -Path 'C:\xampp\htdocs\pelucias' -Target $PWD

# 2. Subir o Apache e o MariaDB
& cmd /c 'start "Apache"  /MIN C:\xampp\apache\bin\httpd.exe'
& cmd /c 'start "MariaDB" /MIN C:\xampp\mysql\bin\mysqld.exe --defaults-file=C:\xampp\mysql\bin\my.ini --standalone'

# 3. Criar o banco e os dados de exemplo (espere uns 5 s depois do passo 2)
& cmd /c '"C:\xampp\mysql\bin\mysql.exe" -u root < database\schema.sql'
& cmd /c '"C:\xampp\mysql\bin\mysql.exe" -u root < database\seed.sql'
```

Pronto:

| | |
|---|---|
| Site | <http://localhost/pelucias/> |
| Painel | <http://localhost/pelucias/admin/> |
| E-mail | `admin@pelucias.com` |
| Senha | `admin123` |

Para conferir que subiu mesmo:

```powershell
(Invoke-WebRequest 'http://localhost/pelucias/' -UseBasicParsing).StatusCode            # 200
& "C:\xampp\mysql\bin\mysql.exe" -u root pelucias -e "SELECT COUNT(*) FROM produtos;"   # 10
```

### Detalhes que economizam tempo

- **O passo 3 recomeça o banco do zero** (`DROP TABLE` + `TRUNCATE`). Em máquina nova é
  exatamente o que se quer; em máquina que já está em uso, **pule** — ele apaga tudo o
  que foi cadastrado pelo painel.
- **O `& cmd /c` dos passos 2 e 3 não é enfeite.** O `<` não existe no PowerShell 5.1
  (dá erro de sintaxe), e o `mysqld` iniciado com `Start-Process` morre junto com o
  shell que o chamou. O `start` do `cmd` resolve os dois casos.
- Não use `Get-Content arquivo.sql | mysql.exe`: a conversão de texto estraga os acentos
  do `seed.sql`.
- O passo 1 falha se `C:\xampp\htdocs\pelucias` já existir. Apague a pasta (ou a junção
  antiga) antes de repetir.
- Se usar outro nome de pasta dentro de `htdocs`, ajuste `URL_BASE` em
  `includes/config.php`. Com o nome `pelucias`, nada precisa ser editado.
- ⚠️ **Troque a senha no primeiro acesso**, em **Alterar senha** dentro do painel. A
  senha padrão está escrita aqui e no `seed.sql` — quem vê o repositório a conhece.

### Trocar os dados da Carol

`includes/config.php` é o **único** arquivo que precisa ser editado:

```php
define('BD_NOME', 'pelucias');        // nome do banco
define('BD_USUARIO', 'root');         // padrão do XAMPP
define('BD_SENHA', '');               // padrão do XAMPP: vazio

define('URL_BASE', '/pelucias');      // pasta dentro do htdocs

define('WHATSAPP', '5511999999999');  // número da Carol: só dígitos, com 55 + DDD
define('INSTAGRAM', 'ponntosepelucias');
define('PIX_CHAVE', 'carol@email.com');
```

---

## O projeto

O site tem duas partes:

- **Catálogo público** — qualquer pessoa acessa, navega por categorias e coleções,
  pesquisa e encomenda pelo WhatsApp.
- **Painel da Carol** (`/admin`) — só com login. É onde ela cadastra produtos,
  fotos, categorias, coleções, banners e acompanha os pedidos.

Não existe carrinho nem pagamento online: o site é uma vitrine. O cliente fecha a
encomenda pelo WhatsApp e paga por **Pix**, combinado direto com a Carol.

## Tecnologias

| Camada | O que usamos |
|---|---|
| Servidor | PHP 8 puro (sem framework, sem Composer) |
| Banco | MySQL com PDO e *prepared statements* |
| Frente | HTML5 semântico, CSS3 próprio (sem Bootstrap) |
| Interações | JavaScript puro, só no carrossel, menu, galeria e prévia de upload |
| Ambiente | XAMPP (Apache + MySQL) |

O site é **mobile-first**: o CSS base é o do celular e as telas maiores entram por
`@media`. Foi desenhado a partir do protótipo em `prototipo_pelucias.pdf`.

---

## Estrutura de pastas

```
pelucias/
├── .htaccess              Bloqueia o download do .git, dos .sql e a listagem de pastas
├── index.php              Home: banner, diferenciais, categorias, coleções, mais vendidos
├── categoria.php          Produtos de uma categoria (?slug=)
├── colecoes.php           Todas as coleções
├── colecao.php            Produtos de uma coleção (?slug=)
├── produto.php            Página do produto + formulário de encomenda (?id=)
├── busca.php              Resultado da pesquisa (?q=)
├── sobre.php              Sobre a Carol e como encomendar
├── encomendar.php         Recebe o POST, grava o pedido e abre o WhatsApp
├── 404.php                Página amigável de "não encontrado"
│
├── includes/
│   ├── config.php         Constantes (banco, URL, WhatsApp, Pix) e início da sessão
│   ├── conexao.php        A única conexão PDO do projeto
│   ├── funcoes.php        e(), formatarPreco(), gerarSlug(), CSRF, flash, upload...
│   ├── header.php         Topo: menu lateral, logo, WhatsApp e busca
│   ├── footer.php         Rodapé e carregamento do JavaScript
│   └── lista_produtos.php A grade de produtos usada por categoria, coleção e busca
│
├── admin/
│   ├── login.php / logout.php / proteger.php
│   ├── index.php          Painel inicial com os números e os últimos pedidos
│   ├── produtos.php       Lista + filtro por categoria e busca
│   ├── produto_form.php   Criar/editar produto (mesmo formulário)
│   ├── produto_excluir.php  Esconder ou excluir produto (POST)
│   ├── produto_foto.php   Apagar uma foto (POST)
│   ├── categorias.php, categoria_form.php, categoria_excluir.php
│   ├── colecoes.php,   colecao_form.php,   colecao_excluir.php
│   ├── banners.php,    banner_form.php,    banner_excluir.php
│   ├── pedidos.php,    pedido_ver.php,     pedido_status.php
│   ├── senha.php          Alterar a própria senha
│   ├── includes/          header, menu lateral e footer do painel
│   └── assets/admin.css
│
├── assets/
│   ├── css/style.css      Todo o visual do site, com as cores em :root
│   ├── js/main.js         Menu, carrossel, galeria e prévia de upload
│   └── img/               favicon e a imagem "sem foto"
│
├── uploads/               Imagens enviadas pelo painel
│   ├── produtos/ banners/ colecoes/ categorias/
│   └── .htaccess          Impede que um .php aqui dentro seja executado
│
├── database/
│   ├── schema.sql         CREATE DATABASE + tabelas
│   └── seed.sql           Dados de exemplo + administrador padrão
│
├── prototipo_pelucias.pdf O protótipo que serviu de base para o layout
└── README.md
```

### As cores (em `assets/css/style.css`)

```css
:root {
  --rosa-fundo: #fff3f7;      /* fundo da página */
  --rosa-principal: #e8789f;  /* logo, títulos, preços */
  --rosa-medio: #f7b6cb;      /* ícones e detalhes */
  --rosa-claro: #fbdce7;      /* bordas e barra de busca */
  --texto: #5c4049;
  --texto-suave: #a98b96;
}
```

Mudou a cor aí, mudou no site inteiro. Fontes: **Fredoka** nos títulos e **Nunito**
no texto, carregadas do Google Fonts.

---

## Como funcionam as tabelas

| Tabela | Para que serve |
|---|---|
| `admins` | Quem entra no painel. A senha é guardada como *hash*, nunca em texto. |
| `categorias` | As bolinhas redondas da home (chaveiros, personagens, animais...). |
| `colecoes` | Kits e conjuntos. |
| `produtos` | O catálogo. `ativo = 0` esconde do site sem apagar. |
| `produto_fotos` | Várias fotos por produto; `principal = 1` é a que aparece no card. |
| `colecao_produto` | Liga produtos e coleções (N:N — um produto pode estar em várias). |
| `banners` | As imagens grandes do carrossel da home. |
| `pedidos` | O que chega pelo formulário de encomenda, com a situação de cada um. |

Duas decisões que valem explicar na apresentação:

- **Apagar uma categoria não apaga os produtos dela** — eles ficam "sem categoria".
  Mesmo assim o painel **recusa** excluir categoria que ainda tenha produtos, e
  sugere escondê-la.
- **Apagar um produto não apaga os pedidos antigos** — o pedido continua no
  histórico, só perde o link para o produto.

---

## Segurança

O que foi feito e onde conferir no código:

| Proteção | Como | Onde |
|---|---|---|
| SQL Injection | 100% das consultas com *prepared statements*; `ATTR_EMULATE_PREPARES = false` | `includes/conexao.php` e todas as páginas |
| XSS | Toda saída passa pela função `e()` (`htmlspecialchars`) | `includes/funcoes.php` |
| CSRF | Token na sessão, campo oculto em todo formulário POST, conferido com `hash_equals` | `csrfCampo()` e `exigirCsrf()` |
| Upload malicioso | Tipo real conferido com `finfo` (só JPG/PNG/WEBP), limite de 3 MB, nome aleatório com `random_bytes` | `salvarImagem()` |
| Execução de PHP nos uploads | `.htaccess` desliga o PHP e nega acesso a arquivos de código | `uploads/.htaccess` |
| Download do `.sql` e do `.git` | `.htaccess` da raiz bloqueia `.git`, `*.sql`, `*.md` e a listagem de pastas | `.htaccess` |
| Roubo de sessão | Cookie `httponly` + `samesite=Lax`; `session_regenerate_id(true)` ao logar e ao trocar a senha | `includes/config.php`, `admin/login.php` |
| Enumeração de usuário | Erro de login é sempre o mesmo texto, não diz se o e-mail existe | `admin/login.php` |
| Exclusão por engano | Toda exclusão é POST com CSRF e confirmação — nunca um link | `*_excluir.php` |
| Dados inválidos | Tudo é validado **no servidor**, não só com o `required` do HTML | `encomendar.php`, `admin/*_form.php` |

---

## Roteiro de teste

Siga na ordem; é o mesmo roteiro que dá para repetir na apresentação.

**Site público**

1. Abrir `http://localhost/pelucias/` — o banner troca sozinho a cada 5 s e
   responde às setas e ao arrastar com o dedo.
2. Estreitar a janela até o tamanho de um celular — as categorias e os "mais
   vendidos" passam a rolar para o lado, e o menu vira hambúrguer.
3. Pesquisar "pinguim" na barra de busca — deve achar os produtos com esse nome.
4. Abrir um produto, trocar a foto grande clicando nas miniaturas.
5. Clicar em **Encomendar pelo WhatsApp**, preencher e enviar — o navegador deve
   abrir o `wa.me` com a mensagem pronta.
6. Conferir no phpMyAdmin que a linha nova apareceu na tabela `pedidos`.
7. Abrir `produto.php?id=9999` — deve cair na página 404 amigável.

**Painel**

8. Entrar em `/pelucias/admin/` com `admin@pelucias.com` / `admin123`.
9. Criar um produto com 2 fotos, marcar uma como principal e deixar "Publicado".
10. Ver o produto novo aparecer no site.
11. Clicar em **Esconder** na lista de produtos e conferir que ele sumiu do site.
12. Abrir o pedido criado no passo 5, mudar a situação para "Em produção".
13. Tentar excluir uma categoria que tenha produtos — deve aparecer o aviso.
14. Trocar a senha em **Alterar senha** e entrar de novo com a nova.

**Segurança**

15. Criar um arquivo `uploads/teste.php` com `<?php echo "oi"; ?>` e abrir
    `http://localhost/pelucias/uploads/teste.php` — **não** pode executar.
    Apague o arquivo depois do teste.
16. Renomear um `.txt` para `.jpg` e tentar enviar como foto — deve ser recusado.

**Checagem de sintaxe** (com o XAMPP instalado, no Git Bash):

```bash
cd /c/xampp/htdocs/pelucias
find . -name "*.php" -exec /c/xampp/php/php.exe -l {} \;
```

Todas as linhas devem dizer `No syntax errors detected`.

### O que já foi verificado

Rodado em 05/10/2026 com XAMPP (Apache 2.4.58, PHP 8.2.12, MySQL), tudo passando:

- `php -l` nos **39 arquivos PHP** — nenhum erro de sintaxe.
- `schema.sql` e `seed.sql` importados do zero: 5 categorias, 2 coleções, 3 banners,
  10 produtos, 12 fotos, 2 pedidos.
- **35 URLs** do site e do painel varridas com `error_reporting(E_ALL)` —
  **zero** warnings, notices ou deprecated.
- Encomenda ponta a ponta: formulário → validação → linha em `pedidos` → redirecionamento
  para o `wa.me` com a mensagem montada. Com erro de validação, a mensagem aparece e o
  formulário volta preenchido.
- Painel: login, dashboard, criação de produto com 2 fotos (preço digitado "72,50"
  virou `72.50`, slug automático, nomes de arquivo aleatórios, primeira foto como
  principal), esconder/publicar, exclusão apagando as fotos do disco, troca de status
  do pedido e troca de senha com login conferido antes e depois.
- Segurança: PHP dentro de `uploads/` responde **403**; `.txt` renomeado para `.jpg` é
  recusado pelo `finfo`; POST sem token CSRF responde **403**; excluir categoria com
  produtos é recusado; status de pedido inventado é recusado; o erro de login é idêntico
  para senha errada e para e-mail inexistente.

Os dados de teste foram apagados depois e o banco está no estado do `seed.sql`.

---

## Se der problema

| Sintoma | Causa provável | O que fazer |
|---|---|---|
| O Apache não sobe | Outro programa já está usando a porta 80 (IIS, Skype, Caddy, nginx, Docker) | No PowerShell: `Get-NetTCPConnection -LocalPort 80 -State Listen` mostra o PID; parar esse programa, **ou** trocar `Listen 80` para `Listen 8080` em `xampp\apache\conf\httpd.conf` e usar `http://localhost:8080/pelucias/` |
| "Não foi possível conectar ao banco de dados" | MariaDB desligado ou banco não importado | Repetir os passos 2 e 3 de **Subir o site**. Para ver se ele está no ar: `Get-NetTCPConnection -LocalPort 3306 -State Listen` |
| O MariaDB sobe e cai sozinho | Foi iniciado com `Start-Process`, e ele morre com o shell | Usar o `& cmd /c 'start ...'` do passo 2. O motivo fica no log `C:\xampp\mysql\data\mysql_error.log` |
| O site aparece sem cor nenhuma, e os links dão 404 | `URL_BASE` não bate com o nome da pasta | Ajustar `URL_BASE` em `includes/config.php` (se a pasta é `htdocs/pelucias`, o valor é `/pelucias`) |
| As imagens de exemplo não aparecem | A pasta `uploads/` não foi copiada junto | Copiar a pasta inteira, com as subpastas `produtos/`, `banners/`, `colecoes/` e `categorias/` |
| O upload diz "Formato não aceito" com um JPG de verdade | Extensão `fileinfo` desligada no PHP | Em `php.ini`, tirar o `;` de `extension=fileinfo` e reiniciar o Apache |
| Upload de arquivo grande não chega ao servidor | Limite do próprio PHP | Em `php.ini`, `upload_max_filesize` e `post_max_size` com pelo menos `8M` |
| "Sessão expirada ou formulário inválido" | A aba ficou aberta tempo demais e o token CSRF caiu | Recarregar a página e enviar de novo |

## Sugestão de divisão do trabalho entre as 4 pessoas

Cada pessoa é dona de uma área, mas todas precisam entender `includes/funcoes.php`,
porque é o arquivo que todo o resto usa.

### Pessoa 1 — Front público
**Arquivos:** `index.php`, `categoria.php`, `colecao.php`, `colecoes.php`,
`busca.php`, `produto.php`, `sobre.php`, `404.php`, `includes/header.php`,
`includes/footer.php`, `includes/lista_produtos.php`.

Responde por: a home sair igual ao protótipo, as listagens com ordenação e
paginação, a página do produto e a navegação entre elas.

### Pessoa 2 — Painel da Carol
**Arquivos:** tudo dentro de `admin/`.

Responde por: login e logout, dashboard, os quatro CRUDs (produtos, categorias,
coleções, banners), a tela de pedidos e o upload de várias fotos.

### Pessoa 3 — Banco e segurança
**Arquivos:** `database/schema.sql`, `database/seed.sql`, `includes/conexao.php`,
`includes/config.php`, `includes/funcoes.php`, `encomendar.php`, `uploads/.htaccess`.

Responde por: o modelo de dados, as consultas, o CSRF, o escape de saída, as regras
de upload e o fluxo de encomenda. É quem apresenta a tabela de segurança acima.

### Pessoa 4 — CSS, responsividade e documentação
**Arquivos:** `assets/css/style.css`, `admin/assets/admin.css`, `assets/js/main.js`,
`assets/img/`, este `README.md`.

Responde por: as cores e fontes baterem com o protótipo, o site funcionar bem em
celular, tablet e desktop, a acessibilidade (alt nas imagens, contraste, navegação
por teclado) e a documentação que o professor vai ler.

### Combinados do grupo

- Ninguém edita arquivo de outra pessoa sem avisar. Mudança em `funcoes.php`
  ou no `:root` do CSS é conversada antes, porque afeta todo mundo.
- Quem terminar uma parte roda o roteiro de teste da sua área antes de dizer que
  acabou.
- As fotos de exemplo em `uploads/` foram recortadas do protótipo só para o site não
  abrir vazio. Elas devem ser trocadas pelas fotos reais da Carol, pelo painel.
