<?php
require_once '../controllers/TutoriaController.php';

$tutoriaController = new TutoriaController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tutoriaController->registrarTutoria();
} else {
    $tutoriaController->showRegistroForm();
}
?>