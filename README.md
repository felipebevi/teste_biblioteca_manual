# Teste Biblioteca

Guia rápido para preparar o ambiente PHP puro (branch `master`) usando Docker e os scripts presentes neste repositório.

## 1. Clonar o projeto

```bash
git clone https://github.com/felipebevi/teste_biblioteca_manual.git
cd teste_biblioteca_manual
git checkout master
```

## 2. Subir os contêineres

```bash
docker compose up -d
```

O serviço `web` disponibiliza a aplicação em http://localhost (porta 80) e o serviço `db` expõe o MySQL na porta 3306.

## 3. Criar e popular as tabelas atuais

O volume do projeto já está montado nos contêineres, então basta executar:

```bash
docker compose exec db bash -c "mysql -ubiblioteca -pbiblioteca biblioteca < /var/www/html/tabelas.sql"
```

Esse comando recria todas as tabelas e a view definidas em `tabelas.sql`, deixando o banco idêntico ao estado atual do projeto.

## 4. Acessar o sistema

Com o Docker no ar e o schema criado, abra http://localhost em seu navegador para usar os cadastros de autor, assunto e livro.

---

## Branch com testes PHPUnit (`phpunit_ia`)

Caso queira validar a versão instrumentada com TDD:

```bash
git fetch origin
git checkout phpunit_ia
composer install
docker compose up -d db
DB_HOST=127.0.0.1 DB_PORT=3306 DB_NAME=biblioteca DB_USER=biblioteca DB_PASSWORD=biblioteca ./vendor/bin/phpunit --verbose
```

O último comando executa a suíte com saída detalhada (`--verbose`). Ao finalizar, volte para a branch principal com `git checkout master`.
