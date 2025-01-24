<?php
require_once '../config/config.php';
require_once '../config/connection.php';
require_once '../models/JefeCarrera.php';

class JefeCarreraController
{
    private $conn;
    private $jefeCarreraModel;

    public function __construct()
    {
        $this->conn = connectiondb();
        $this->jefeCarreraModel = new JefeCarrera($this->conn);
    }

    public function showJefesCarrera()
    {
        session_start();

        $rolesPermitidos = [3];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
            unset($_SESSION['message']);
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $user = $_SESSION['user'];
        $csrf_token = $_SESSION['csrf_token'];

        $jefesCarrera = $this->jefeCarreraModel->getJefesCarrera();

        require_once '../views/administrarJefesCarrera.php';
    }

    public function showCreateForm()
    {
        session_start();

        $rolesPermitidos = [3];
        if (!isset($_SESSION['user']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $user = $_SESSION['user'];
        $csrf_token = $_SESSION['csrf_token'];
        $rol = 5; // Rol de jefe de carrera

        if (isset($_SESSION['errors'])) {
            $errors = $_SESSION['errors'];
            unset($_SESSION['errors']);
        }

        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
            unset($_SESSION['message']);
        }

        require_once '../views/registroJefeCarrera.php';
    }

    public function createJefeCarrera()
    {
        session_start();

        $rolesPermitidos = [3];
        if (!isset($_SESSION['user']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                echo "Error: Solicitud no válida.";
                exit();
            }

            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $apellidoPaterno = isset($_POST['paterno']) ? trim($_POST['paterno']) : '';
            $apellidoMaterno = isset($_POST['materno']) ? trim($_POST['materno']) : '';
            $noPersonal = isset($_POST['noPersonal']) ? trim($_POST['noPersonal']) : '';
            $correoInstitucional = isset($_POST['correoInstitucional']) ? trim($_POST['correoInstitucional']) : '';
            $rol = isset($_POST['rol']) ? intval($_POST['rol']) : 5; // Rol de jefe de carrera

            $errors = [];

            if (empty($nombre))
                $errors[] = 'El campo "Nombre" es obligatorio.';
            if (empty($correoInstitucional))
                $errors[] = 'El campo "Correo institucional" es obligatorio.';

            if (
                !empty($correoInstitucional) &&
                !preg_match('/^.+@(uv\.mx|estudiantes\.uv\.mx)$/', $correoInstitucional)
            ) {
                $errors[] = 'El correo institucional debe terminar en @uv.mx o @estudiantes.uv.mx.';
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header('Location: ' . BASE_URL . '/registroJefeCarrera.php');
                exit();
            }

            $data = [
                'nombre' => $nombre,
                'apellidoPaterno' => $apellidoPaterno,
                'apellidoMaterno' => $apellidoMaterno,
                'noPersonal' => $noPersonal,
                'correoInstitucional' => $correoInstitucional,
                'rol' => $rol
            ];

            $resultado = $this->jefeCarreraModel->createJefeCarrera($data);

            if ($resultado) {
                $_SESSION['message'] = "Jefe de carrera registrado exitosamente.";
                header("Location: " . BASE_URL . "/administrarJefesCarrera.php");
                exit();
            } else {
                $_SESSION['message'] = "Error al registrar el jefe de carrera.";
                header("Location: " . BASE_URL . "/registroJefeCarrera.php");
                exit();
            }
        } else {
            header("Location: " . BASE_URL . "/registroJefeCarrera.php");
            exit();
        }
    }
}