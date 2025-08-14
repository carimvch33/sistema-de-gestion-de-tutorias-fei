<?php
require_once '../controllers/ReporteController.php';

$idCarrera = $_POST['idCarrera'] ?? null;
$idReporteActual = $_POST['idReporteActual'] ?? null;
$controller = new ReporteController();
$controller->getSesionesTutoria($idCarrera, $idReporteActual);
?>