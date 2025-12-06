<?php

// host do servico MySQL definido no docker-compose (service name: db)
$servername = "db";
$username = "biblioteca";
$password = "biblioteca";
$dbname = "biblioteca";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
