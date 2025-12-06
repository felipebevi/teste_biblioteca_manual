<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$defaults = [
    'DB_HOST' => getenv('DB_HOST') ?: '127.0.0.1',
    'DB_PORT' => getenv('DB_PORT') ?: '3306',
    'DB_USER' => getenv('DB_USER') ?: 'biblioteca',
    'DB_PASSWORD' => getenv('DB_PASSWORD') ?: 'biblioteca',
    'DB_NAME' => getenv('DB_NAME') ?: 'biblioteca',
];

foreach ($defaults as $key => $value) {
    putenv($key . '=' . $value);
    $_ENV[$key] = $value;
}

function wait_for_database(array $config): void
{
    $attempts = 0;
    $maxAttempts = 10;
    while ($attempts < $maxAttempts) {
        $conn = @new mysqli(
            $config['host'],
            $config['user'],
            $config['password'],
            $config['dbname'],
            (int)$config['port']
        );
        if ($conn && $conn->connect_errno === 0) {
            $conn->close();
            return;
        }
        $attempts++;
        sleep(1);
    }

    throw new RuntimeException('Nao foi possivel conectar ao banco de dados para executar os testes.');
}

wait_for_database([
    'host' => $_ENV['DB_HOST'],
    'port' => $_ENV['DB_PORT'],
    'user' => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASSWORD'],
    'dbname' => $_ENV['DB_NAME'],
]);
