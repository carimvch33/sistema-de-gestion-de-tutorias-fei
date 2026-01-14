<?php
require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

define('BASE_URL', $_ENV['BASE_URL']);

function isProduction()
{
    return $_ENV['ENVIRONMENT'] === 'production';
}

// Cargar manejo centralizado de errores
require_once __DIR__ . '/error_handler.php';
?>