<?php
// Esto encenderá las alertas para que el Error 500 nos muestre texto en lugar de ocultarse
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// El ../ ES correcto porque así encuentra el controlador. 
require_once '../controllers/ProfesorController.php';

$controller = new ProfesorController();
$controller->importarProfesoresCSV();
?>