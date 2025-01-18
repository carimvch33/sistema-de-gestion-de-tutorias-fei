<?php
require_once '../config/config.php';
require_once '../controllers/AuthController.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$authController = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController->login($_POST);
}

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

require '../views/login.php';
?>