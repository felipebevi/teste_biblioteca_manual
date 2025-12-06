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

O arquivo `tabelas.sql` e executado automaticamente pelo MySQL no primeiro start (esta montado em `docker-entrypoint-initdb.d`). Para uma carga limpa do schema/dados, use apenas este comando:

```bash
docker compose down -v && docker compose up -d
```

O `db` so fica saudavel depois que o MySQL termina de iniciar e rodar o script; aguarde alguns segundos. Para conferir se as tabelas foram criadas:

```bash
docker exec mysql_biblioteca mysql -uroot -proot -e "show tables;" biblioteca
```

Se ocorrer conflito de nomes de container, derrube antes com `docker compose down -v` (ou `docker rm -f mysql_biblioteca php_apache`) e repita o comando unico acima.

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
