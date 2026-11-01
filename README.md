# PontosPelucias

Site da loja Pontos e Pelúcias (by Carol). Os produtos ficam num banco MariaDB e as páginas de catálogo e detalhe são geradas em PHP.

## Como rodar no seu PC (XAMPP)

1. **Clone o projeto dentro do `htdocs` do XAMPP** (o Apache só serve arquivos de lá):
   ```
   cd C:\xampp\htdocs
   git clone <url-do-repositorio> PontosPelucias
   ```
2. No XAMPP Control Panel, dê **Start** em **Apache** e **MySQL**.
3. **Importe o banco**: abra http://localhost/phpmyadmin → aba **Importar** → escolha `banco/pontospelucias.sql` → **Executar**.
   Ou pelo terminal: `C:\xampp\mysql\bin\mysql.exe -u root < banco/pontospelucias.sql`
4. Acesse http://localhost/PontosPelucias/

Se o seu MySQL tiver usuário/senha diferente de `root` sem senha, ajuste em `includes/conexao.php`.

## Estrutura

| Arquivo | O que faz |
|---|---|
| `banco/pontospelucias.sql` | Cria o banco `pontospelucias` e a tabela `produtos` com todos os itens |
| `includes/conexao.php` | Conexão com o banco (PDO) |
| `includes/cabecalho.php` | Cabeçalho e menu usados pelas páginas PHP |
| `catalogo.php` | Lista os produtos do banco |
| `informacao.php?id=N` | Mostra os detalhes do produto `N` |

## Adicionar ou alterar produtos

1. Coloque a imagem em `img/`.
2. Edite os dados pelo phpMyAdmin (tabela `produtos`) — o site atualiza na hora.
3. Para os parceiros receberem a mudança, atualize também o `INSERT` em `banco/pontospelucias.sql` e faça commit. Quem puxar reimporta o arquivo.

Para esconder um produto sem apagar, mude `ativo` para `0`. Preço, cores ou opções vazios (`NULL`) aparecem como "sob consulta" / "Sem opções".
