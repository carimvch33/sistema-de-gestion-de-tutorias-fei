<?php
require_once '../config/config.php';
require_once '../config/connection.php';
require_once '../models/Problematica.php';
require_once '../models/TipoProblematica.php';

class ProblematicaController
{
    private $conn;
    private $problematicaModel;
    private $tipoProblematicaModel;

    public function __construct()
    {
        $this->conn = connectiondb();
        $this->problematicaModel = new Problematica($this->conn);
        $this->tipoProblematicaModel = new TipoProblematica($this->conn);
    }

    public function showProblematicas()
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

        $problematicas = $this->problematicaModel->getProblematicas();

        require_once '../views/administrarProblematicas.php';
    }

    public function showCreateForm()
    {
        session_start();
        $rolesPermitidos = [3];

        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $csrf_token = $_SESSION['csrf_token'];
        $user = $_SESSION['user'];

        if (isset($_SESSION['errors'])) {
            $errors = $_SESSION['errors'];
            unset($_SESSION['errors']);
        }

        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
            unset($_SESSION['message']);
        }

        // Obtener los tipos de problemática
        $tiposProblematica = $this->tipoProblematicaModel->getTipos();

        require_once '../views/registroProblematica.php';
    }

    public function createProblematica()
    {
        session_start();
        $rolesPermitidos = [3];

        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                echo "Error: Solicitud no válida.";
                exit();
            }

            $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
            $tipoProblematica = isset($_POST['tipoProblematica']) ? intval($_POST['tipoProblematica']) : 0;

            $errors = [];

            if (empty($descripcion)) {
                $errors[] = 'El campo "Descripción" es obligatorio.';
            }
            if ($tipoProblematica <= 0) {
                $errors[] = 'El campo "Tipo de Problematica" es obligatorio.';
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header('Location: ' . BASE_URL . '/registroProblematica.php');
                exit();
            }

            $resultado = $this->problematicaModel->createProblematica($descripcion, $tipoProblematica);

            if ($resultado) {
                $_SESSION['message'] = "Problemática académica registrada exitosamente.";
                header("Location: " . BASE_URL . "/administrarProblematicas.php");
                exit();
            } else {
                $_SESSION['message'] = "Error al registrar la problemática académica.";
                header('Location: ' . BASE_URL . '/registroProblematica.php');
                exit();
            }
        } else {
            header('Location: ' . BASE_URL . '/registroProblematica.php');
            exit();
        }
    }

    public function showEditForm()
    {
        session_start();
        $rolesPermitidos = [3];

        if (!isset($_SESSION['user']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idProblematica = isset($_POST['idProblematica']) ? intval($_POST['idProblematica']) : 0;

            if ($idProblematica > 0) {
                $problematica = $this->problematicaModel->getProblematicaById($idProblematica);

                if ($problematica) {
                    $_SESSION['problematica'] = $problematica;

                    if (empty($_SESSION['csrf_token'])) {
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    }
                    $csrf_token = $_SESSION['csrf_token'];
                    $user = $_SESSION['user'];

                    // Obtener los tipos de problemática
                    $tiposProblematica = $this->tipoProblematicaModel->getTipos();

                    require_once '../views/editarProblematica.php';
                } else {
                    $_SESSION['message'] = 'Problemática no encontrada';
                    header("Location: " . BASE_URL . "/administrarProblematicas.php");
                }
            } else {
                $_SESSION['message'] = 'ID de problemática inválido';
                header("Location: " . BASE_URL . "/administrarProblematicas.php");
            }
        } else {
            header("Location: " . BASE_URL . "/administrarProblematicas.php");
        }
    }

    public function updateProblematica()
    {
        session_start();
        $rolesPermitidos = [3];

        if (!isset($_SESSION['user']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die('Error: solicitud inválida o CSRF token no válido.');
            }

            $idProblematica = isset($_POST['idProblematica']) ? intval($_POST['idProblematica']) : 0;
            $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
            $tipoProblematica = isset($_POST['tipoProblematica']) ? intval($_POST['tipoProblematica']) : 0;

            $errors = [];

            if ($idProblematica <= 0)
                $errors[] = 'ID de problemática inválido.';
            if (empty($descripcion))
                $errors[] = 'El campo "Descripción" es obligatorio.';
            if ($tipoProblematica <= 0)
                $errors[] = 'El campo "Tipo de Problemática" es obligatorio.';

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header('Location: ' . BASE_URL . '/editarProblematica.php');
                exit();
            }

            $resultado = $this->problematicaModel->updateProblematica($idProblematica, $descripcion, $tipoProblematica);

            if ($resultado) {
                $_SESSION['message'] = 'Problemática académica actualizada exitosamente.';
                header("Location: " . BASE_URL . "/administrarProblematicas.php");
            } else {
                $_SESSION['message'] = 'Error al actualizar la problemática académica.';
                header('Location: ' . BASE_URL . '/editarProblematica.php');
            }
        } else {
            header("Location: " . BASE_URL . "/administrarProblematicas.php");
        }
    }

    public function deleteProblematica()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                echo json_encode(['status' => 'error', 'message' => 'Token CSRF inválido.']);
                exit();
            }

            $rolesPermitidos = [3];
            if (!isset($_SESSION['user']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
                echo json_encode(['status' => 'error', 'message' => 'Acceso denegado.']);
                exit();
            }

            $idProblematica = isset($_POST['idProblematica']) ? intval($_POST['idProblematica']) : 0;

            if ($idProblematica > 0) {
                $resultado = $this->problematicaModel->deleteProblematica($idProblematica);

                if ($resultado) {
                    echo json_encode(['status' => 'success', 'message' => 'Problemática eliminada con éxito.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error al eliminar la problemática.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ID de problemática inválido.']);
            }
        } else {
            header("Location: " . BASE_URL . "/administrarProblematicas.php");
        }
    }
}