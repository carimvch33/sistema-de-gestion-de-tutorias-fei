<?php
require_once '../config/config.php';

function connectiondb()
{
    $host = $_ENV['DB_HOST'];
    $port = (int) $_ENV['DB_PORT'];
    $socket = $_ENV['DB_SOCKET'];
    $user = $_ENV['DB_USER'];
    $password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    $conn = new mysqli($host, $user, $password, $dbname, $port, $socket);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>