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
    // DEF-32: Manejo centralizado de errores y excepciones
    // Deshabilitar reportes de error de mysqli para manejarlos con excepciones
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    try {
        $conn = new mysqli($host, $user, $password, $dbname, $port, $socket);
        $conn->set_charset("utf8");
        return $conn;
    } catch (mysqli_sql_exception $e) {
        // El error será capturado por el exception handler global
        throw new Exception("No se pudo conectar a la base de datos. Por favor, verifica tu conexión.", 0, $e);
    }
}
?>