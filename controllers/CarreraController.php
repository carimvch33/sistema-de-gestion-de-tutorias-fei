<?php
require_once '../config/connection.php';
require_once '../models/Carrera.php';

class CarreraController
{
    private $conn;
    private $carreraModel;

    public function __construct()
    {
        $this->conn = connectiondb();
        $this->carreraModel = new Carrera($this->conn);
    }

    public function showCarreras()
    {
        session_start();

        $rolesPermitidos = [3];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ./cerrarSesion.php');
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

        $carreras = $this->carreraModel->getCarreras();

        require_once '../views/administrarCarreras.php';
    }

    public function showCreateForm()
    {
        session_start();

        $rolesPermitidos = [3];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ./cerrarSesion.php');
            exit();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $csrf_token = $_SESSION['csrf_token'];
        $user = $_SESSION['user'];

        require_once '../views/registroCarrera.php';
    }

    public function createCarrera()
    {
        session_start();

        $rolesPermitidos = [3];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ./cerrarSesion.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $_SESSION['message'] = "Error: Solicitud no válida.";
                header('Location: ./registroCarrera.php');
                exit();
            }

            $carrera = !empty($_POST['carrera']) ? trim($_POST['carrera']) : null;

            $errors = [];
            if (empty($carrera))
                $errors[] = 'El campo "Carrera" es obligatorio.';

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header('Location: ./registroCarrera.php');
                exit();
            }

            $result = $this->carreraModel->createCarrera($carrera);

            if ($result) {
                $_SESSION['message'] = "Carrera registrada exitosamente.";
                header("Location: ./administrarCarreras.php");
                exit();
            } else {
                $_SESSION['message'] = "Error al registrar la carrera.";
                header("Location: ./administrarCarreras.php");
                exit();
            }
        } else {
            header('Location: ./registroCarrera.php');
            exit();
        }
    }

    public function editarCarrera()
    {
        session_start();

        $rolesPermitidos = [3];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ./cerrarSesion.php');
            exit();
        }

        $idCarrera = $_POST['idCarrera'] ?? '';

        if (!empty($idCarrera)) {
            $carrera = $this->carreraModel->getCarreraById($idCarrera);

            if ($carrera) {
                $_SESSION['carrera'] = $carrera;
                header('Location: ./editarCarrera.php');
                exit();
            } else {
                $_SESSION['message'] = 'Carrera no encontrada';
                header('Location: ./administrarCarreras.php');
                exit();
            }
        } else {
            $_SESSION['message'] = 'ID de carrera inválido';
            header('Location: ./administrarCarreras.php');
            exit();
        }
    }

    public function showEditForm()
    {
        session_start();

        $rolesPermitidos = [3];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ./cerrarSesion.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idCarrera = isset($_POST['idCarrera']) ? intval($_POST['idCarrera']) : 0;

            if ($idCarrera > 0) {
                $carrera = $this->carreraModel->getCarreraById($idCarrera);

                if ($carrera) {
                    $_SESSION['carrera'] = $carrera;

                    $csrf_token = $_SESSION['csrf_token'];
                    $user = $_SESSION['user'];

                    require_once '../views/editarCarrera.php';
                } else {
                    $_SESSION['message'] = 'Carrera no encontrada';
                    header('Location: ./administrarCarreras.php');
                    exit();
                }
            } else {
                $_SESSION['message'] = 'ID de carrera inválido';
                header('Location: ./administrarCarreras.php');
                exit();
            }
        } else {
            header('Location: ./administrarCarreras.php');
            exit();
        }
    }

    public function updateCarrera()
    {
        session_start();

        $rolesPermitidos = [3];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ./cerrarSesion.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $_SESSION['message'] = "Error: Solicitud no válida.";
                header('Location: ./administrarCarreras.php');
                exit();
            }

            $idCarrera = isset($_POST['idCarrera']) ? intval($_POST['idCarrera']) : 0;
            $nombreCarrera = isset($_POST['carrera']) ? trim($_POST['carrera']) : '';

            $errors = [];
            if (empty($nombreCarrera)) {
                $errors[] = 'El campo "Carrera" es obligatorio.';
            }

            if ($idCarrera <= 0) {
                $errors[] = 'ID de carrera inválido.';
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header('Location: ./editarCarrera.php');
                exit();
            }

            $result = $this->carreraModel->updateCarrera($idCarrera, $nombreCarrera);

            if ($result) {
                $_SESSION['message'] = "Carrera actualizada exitosamente.";
                header("Location: ./administrarCarreras.php");
                exit();
            } else {
                $_SESSION['message'] = "Error al actualizar la carrera.";
                header("Location: ./administrarCarreras.php");
                exit();
            }
        } else {
            header('Location: ./administrarCarreras.php');
            exit();
        }
    }

    public function deleteCarrera()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                echo json_encode(['status' => 'error', 'message' => 'Token CSRF inválido.']);
                exit();
            }

            $rolesPermitidos = [3];
            if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
                echo json_encode(['status' => 'error', 'message' => 'Acceso denegado.']);
                exit();
            }

            $idCarrera = isset($_POST['idCarrera']) ? intval($_POST['idCarrera']) : 0;

            if ($idCarrera > 0) {
                $resultado = $this->carreraModel->deleteCarrera($idCarrera);

                if ($resultado) {
                    echo json_encode(['status' => 'success', 'message' => 'Carrera eliminada con éxito.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error al eliminar la carrera.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ID de carrera inválido.']);
            }
        } else {
            header('Location: ./administrarCarreras.php');
            exit();
        }
    }
}