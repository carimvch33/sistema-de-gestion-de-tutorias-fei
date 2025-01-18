<?php
require_once '../controllers/ImportacionController.php';

$controller = new ImportacionController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->importarDatos();
} else {
    $controller->showImportForm();
}