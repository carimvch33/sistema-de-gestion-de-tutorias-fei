<?php
require_once '../controllers/ReporteController.php';

$idCarrera = $_POST['idCarrera'] ?? null;
$controller = new ReporteController();
$controller->getSesionesTutoria($idCarrera);
?>