<?php
require_once '../config/config.php';
require_once '../config/connection.php';
require_once '../models/TipoProblematica.php';

class TipoProblematicaController
{
    private $conn;
    private $tipoProblematicaModel;

    public function __construct()
    {
        $this->conn = connectiondb();
        $this->tipoProblematicaModel = new TipoProblematica($this->conn);
    }

    public function showTiposProblematicas()
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

        $problematicas = $this->tipoProblematicaModel->getTipos();

        require_once '../views/administrarTiposProblematicas.php';
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

        require_once '../views/registroTipoProblematica.php';
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
            $idTipoProblematica = isset($_POST['idTipoProblematica']) ? intval($_POST['idTipoProblematica']) : 0;

            if ($idTipoProblematica > 0) {
                $tipoProblematica = $this->tipoProblematicaModel->getTiposProblematicasById($idTipoProblematica);

                if ($tipoProblematica) {
                    $_SESSION['tipoProblematica'] = $tipoProblematica;

                    if (empty($_SESSION['csrf_token'])) {
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    }
                    $csrf_token = $_SESSION['csrf_token'];
                    $user = $_SESSION['user'];

                    require_once '../views/editarTipoProblematica.php';
                } else {
                    $_SESSION['message'] = 'Tipo de problemática no encontrada';
                    header("Location: " . BASE_URL . "/administrarTiposProblematicas.php");
                }
            } else {
                $_SESSION['message'] = 'ID de tipo de problemática inválido';
                header("Location: " . BASE_URL . "/administrarTiposProblematicas.php");
            }
        } else {
            header("Location: " . BASE_URL . "/administrarTiposProblematicas.php");
        }
    }

    public function createTipoProblematica()
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

            $name = isset($_POST['name']) ? trim($_POST['name']) : '';

            $errors = [];

            if (empty($name)) {
                $errors[] = 'El campo "Nombre" es obligatorio.';
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header('Location: ' . BASE_URL . '/registroTipoProblematica.php');
                exit();
            }

            $resultado = $this->tipoProblematicaModel->createProblematica($name);

            if ($resultado) {
                $_SESSION['message'] = "Tipo de Problemática registrada exitosamente.";
                header("Location: " . BASE_URL . "/administrarTiposProblematicas.php");
                exit();
            } else {
                // Pasamos el error de duplicidad a rojo
                if (isset($_SESSION['message'])) {
                    $_SESSION['errors'] = [$_SESSION['message']];
                    unset($_SESSION['message']);
                } else {
                    $_SESSION['errors'] = ["Error al registrar el tipo de problemática."];
                }
                
                header("Location: " . BASE_URL . "/registroTipoProblematica.php");
                exit();
            }
        } else {
            header("Location: " . BASE_URL . "/registroTipoProblematica.php");
            exit();
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

            $idTipoProblematica = isset($_POST['idTipoProblematica']) ? intval($_POST['idTipoProblematica']) : 0;
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';

            $errors = [];

            if ($idTipoProblematica <= 0) $errors[] = 'ID de tipo de problemática inválido.';
            if (empty($name)) $errors[] = 'El campo "Nombre" es obligatorio.';

            $recargarVista = function() use ($idTipoProblematica, $name) {
                $_SESSION['tipoProblematica'] = [
                    'idTipoProblematica' => $idTipoProblematica, 
                    'nombre' => $name 
                ];
                $user = $_SESSION['user'];
                $csrf_token = $_SESSION['csrf_token'];
                
                require_once '../views/editarTipoProblematica.php';
                exit();
            };

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $recargarVista();
            }

            $resultado = $this->tipoProblematicaModel->updateTipoProblematica($idTipoProblematica, $name);

            if ($resultado) {
                $_SESSION['message'] = 'Tipo de problemática actualizada exitosamente.';
                header("Location: " . BASE_URL . "/administrarTiposProblematicas.php");
                exit();
            } else {
                if (isset($_SESSION['message'])) {
                    $_SESSION['errors'][] = $_SESSION['message'];
                    unset($_SESSION['message']);
                } else {
                    $_SESSION['errors'][] = 'Error al actualizar el tipo de problemática.';
                }
                
                $recargarVista();
            }
        } else {
            header("Location: " . BASE_URL . "/administrarTiposProblematicas.php");
            exit();
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

            $idTipoProblematica = isset($_POST['idTipoProblematica']) ? intval($_POST['idTipoProblematica']) : 0;

            if ($idTipoProblematica > 0) {
                $resultado = $this->tipoProblematicaModel->deleteProblematica($idTipoProblematica);

                if ($resultado) {
                    echo json_encode(['status' => 'success', 'message' => 'Tipo de problemática eliminada con éxito.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el tipo de problemática.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ID de tipo de problemática inválido.']);
            }
        } else {
            header("Location: " . BASE_URL . "/administrarTiposProblematicas.php");
        }
    }
}