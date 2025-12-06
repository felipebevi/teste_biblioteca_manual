<?php

// abre conexao ao mysql localhost (pode ser sobrescrito via variáveis de ambiente para testes)
$servername = getenv('DB_HOST') ?: "mysql_biblioteca";
$username = getenv('DB_USER') ?: "biblioteca";
$password = getenv('DB_PASSWORD') ?: "biblioteca";
$dbname = getenv('DB_NAME') ?: "biblioteca";
$port = intval(getenv('DB_PORT') ?: 3306);

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
