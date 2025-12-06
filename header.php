<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Biblioteca</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/estilos.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body class="bg-light">

    <div class="container mt-5 text-center">
        <h1 class="display-4 text-primary">Biblioteca</h1>
        <ul class="nav nav-pills justify-content-center gap-3 mt-4">
            <li class="nav-item">
                <a class="nav-link" href="/">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/cadLivro.php">Cadastro de Livro</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/cadAutor.php">Cadastro de Autor</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/cadAssunto.php">Cadastro de Assunto</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/relatorios.php">Relatórios</a>
            </li>
        </ul>
    </div>
    <hr>

    <div class="container">
<?php
// Conexão com o banco de dados
include_once(dirname(__FILE__) . '/db.php');