<?php
require_once '../config/connection.php';
require_once '../models/Tutoria.php';
require_once '../models/Coordinador.php';

class TutoriasCoordinador
{
    private $conn;
    private $tutoriaModel;
    private $coordinadorModel;

    public function __construct()
    {
        $this->conn = connectiondb();
        $this->tutoriaModel = new Tutoria($this->conn);
        $this->coordinadorModel = new Coordinador($this->conn);
    }

    public function showTutorias()
    {
        session_start();

        $rolesPermitidos = [4]; // Rol para coordinador
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: /cerrarSesion.php');
            exit();
        }

        $user = $_SESSION['user'];
        $correoCoordinador = $_SESSION['correoInstitucional'];

        $csrf_token = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $csrf_token;

        // Obtener idSesion del coordinador utilizando el modelo
        $idSesion = $this->coordinadorModel->getIdSesionByCorreo($correoCoordinador);

        if ($idSesion) {
            $_SESSION['idSesion'] = $idSesion;

            // Obtener las tutorías para las carreras asociadas al coordinador
            $tutorias = $this->tutoriaModel->getTutoriasByCoordinador($idSesion);
        } else {
            // Manejar el caso en que no se encuentre el idSesion (esto no debería ocurrir)
            $tutorias = [];
        }

        // Renderizar la vista
        require_once '../views/consultarTutorias.php';
    }

}
?>