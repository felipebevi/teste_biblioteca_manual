# Teste Biblioteca

Guia rapido para preparar o ambiente PHP puro (branch `master`) usando Docker.

## 1. Clonar o projeto

```bash
git clone https://github.com/felipebevi/teste_biblioteca_manual.git
cd teste_biblioteca_manual
git checkout master
```

## 2. Subir os containers

```bash
docker compose up -d
```

O servico `web` expoe a aplicacao em http://localhost (porta 80) e o servico `db` expoe o MySQL na porta 3306.

## 3. Criar e popular as tabelas

O arquivo `tabelas.sql` e montado no container do MySQL em `/tabelas.sql`. Para recriar o schema atual, use apenas este comando (utiliza o usuario root do MySQL):

```bash
docker compose exec db bash -c "mysql -uroot -proot biblioteca < /tabelas.sql"
```

Se aparecer erro de permissao, limpe dados antigos removendo o volume `mysql_data` antes de subir novamente (`rm -rf mysql_data && docker compose up -d`).

## 4. Acessar o sistema

Com o Docker no ar e o schema criado, abra http://localhost em seu navegador para usar os cadastros de autor, assunto e livro.

---

## Branch com testes PHPUnit (`phpunit_ia`)

Caso queira validar a versao instrumentada com TDD:

```bash
git fetch origin
git checkout phpunit_ia
composer install
docker compose up -d db
DB_HOST=127.0.0.1 DB_PORT=3306 DB_NAME=biblioteca DB_USER=biblioteca DB_PASSWORD=biblioteca ./vendor/bin/phpunit --verbose
```

O ultimo comando executa a suite com saida detalhada (`--verbose`). Ao finalizar, volte para a branch principal com `git checkout master`.
