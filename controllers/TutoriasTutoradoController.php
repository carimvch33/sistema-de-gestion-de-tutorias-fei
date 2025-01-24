<?php
require_once '../config/connection.php';
require_once '../models/Tutoria.php';

class TutoriasTutoradoController
{
    private $conn;
    private $tutoriaModel;
    private $coordinadorModel;

    public function __construct()
    {
        $this->conn = connectiondb();
        $this->tutoriaModel = new Tutoria($this->conn);
    }

    public function showSessions()
    {
        session_start();

        $rolesPermitidos = [2]; 
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: /cerrarSesion.php');
            exit();
        }

        $user = $_SESSION['user'];
        $correoTutorado = $_SESSION['correoInstitucional'];

        $sesiones = $this->tutoriaModel->getSesionesByTutorado($correoTutorado);

        $data = [
            'user' => $user,
            'sesiones' => $sesiones
        ];

        require_once '../views/sesionesTutoria.php';
    }
}

?>