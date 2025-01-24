<?php
require_once '../config/config.php';

function connectiondb()
{
    $host = DB_HOST;
    $port = 3306;
    $socket = "";
    $user = "sistema_regitro_tutorias_usuario";
    $password = "wyR8y1pLjj216KQ";
    $dbname = "sistema_registro_tutorias";
    $conn = new mysqli($host, $user, $password, $dbname, $port, $socket);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>