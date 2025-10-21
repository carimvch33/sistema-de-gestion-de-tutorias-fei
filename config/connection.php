<?php
require_once __DIR__ . '/config.php';

function connectiondb()
{
    $env = $_ENV['ENVIRONMENT'];

    if ($env === 'testing') {
        $host = $_ENV['TEST_DB_HOST'];
        $port = (int) $_ENV['TEST_DB_PORT'];
        $socket = $_ENV['TEST_DB_SOCKET'];
        $user = $_ENV['TEST_DB_USER'];
        $password = $_ENV['TEST_DB_PASSWORD'];
        $dbname = $_ENV['TEST_DB_NAME'];
    } else {
        $host = $_ENV['DB_HOST'];
        $port = (int) $_ENV['DB_PORT'];
        $socket = $_ENV['DB_SOCKET'];
        $user = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
    }
    $conn = new mysqli($host, $user, $password, $dbname, $port, $socket);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>