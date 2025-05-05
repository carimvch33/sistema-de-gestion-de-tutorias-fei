<?php
require_once '../controllers/TutoriasCoordinador.php';

$coordinatorController = new TutoriasCoordinador();
$coordinatorController->generatePDFReportSummary();
?>