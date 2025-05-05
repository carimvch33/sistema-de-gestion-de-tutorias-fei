<?php
ob_start();
require('../vendor/fpdf/fpdf.php');
require_once '../config/config.php';

$coordinatorFullName = htmlspecialchars($coordinator['nombre'] . ' ' . $coordinator['apellidoPaterno'] . ' ' . $coordinator['apellidoMaterno'], ENT_QUOTES, 'UTF-8');
$period = htmlspecialchars($reports[0]['periodo'] ?? '', ENT_QUOTES, 'UTF-8');
$numTutoringSession = htmlspecialchars($numTutoringSession, ENT_QUOTES, 'UTF-8');
