<?php

// abre conexao ao mysql localhost
$servername = "mysql_biblioteca";
$username = "biblioteca";
$password = "biblioteca";
$dbname = "biblioteca";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
