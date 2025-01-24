<?php
require_once '../config/config.php';
require_once '../config/connection.php';
require_once '../models/ExperienciaEducativa.php';
require_once '../models/Carrera.php';

class ExperienciaEducativaController
{
    private $conn;
    private $experienciaModel;
    private $carreraModel;

    public function __construct()
    {
        $this->conn = connectiondb();
        $this->experienciaModel = new ExperienciaEducativa($this->conn);
        $this->carreraModel = new Carrera($this->conn);
    }

    public function showExperiencias()
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

        $experiencias = $this->experienciaModel->getExperiencias();

        require_once '../views/administrarExperienciasEducativas.php';
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

        $user = $_SESSION['user'];
        $csrf_token = $_SESSION['csrf_token'];

        $programas = $this->carreraModel->getCarreras();

        if (isset($_SESSION['errors'])) {
            $errors = $_SESSION['errors'];
            unset($_SESSION['errors']);
        }

        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
            unset($_SESSION['message']);
        }

        require_once '../views/registroExperienciaEducativa.php';
    }
    public function createExperiencia()
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

            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
            $programa = isset($_POST['programa']) ? intval($_POST['programa']) : null;

            $errors = [];

            if (empty($nombre))
                $errors[] = 'El campo "Nombre" es obligatorio.';
            
            if (empty($programa))
                $errors[] = 'El campo "Programa" es obligatorio.';

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header('Location: ' . BASE_URL . '/registroExperienciaEducativa.php');
                exit();
            }

            $result = $this->experienciaModel->createExperiencia($nombre, $programa);

            if ($result) {
                $_SESSION['message'] = "Experiencia educativa registrada exitosamente.";
                header('Location: ' . BASE_URL . '/administrarExperienciasEducativas.php');
                exit();
            } else {
                $_SESSION['message'] = "Error al registrar la experiencia educativa.";
                header("Location: " . BASE_URL . "/registroExperienciaEducativa.php");
                exit();
            }
        } else {
            header('Location: ' . BASE_URL . '/registroExperienciaEducativa.php');
            exit();
        }
    }

    public function showEditForm()
    {
        session_start();

        $rolesPermitidos = [3];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idExperiencia = isset($_POST['idExperiencia']) ? intval($_POST['idExperiencia']) : 0;

            if ($idExperiencia > 0) {
                $experiencia = $this->experienciaModel->getExperienciaById($idExperiencia);

                if ($experiencia) {
                    $_SESSION['experiencia'] = $experiencia;

                    if (empty($_SESSION['csrf_token'])) {
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    }

                    $user = $_SESSION['user'];
                    $csrf_token = $_SESSION['csrf_token'];

                    $programas = $this->carreraModel->getCarreras();

                    require_once '../views/editarExperienciaEducativa.php';
                } else {
                    $_SESSION['message'] = 'Experiencia educativa no encontrada';
                    header('Location: ' . BASE_URL . '/administrarExperienciasEducativas.php');
                    exit();
                }
            } else {
                $_SESSION['message'] = 'ID de experiencia educativa inválido';
                header('Location: ' . BASE_URL . '/administrarExperienciasEducativas.php');
                exit();
            }
        } else {
            header('Location: ' . BASE_URL . '/administrarExperienciasEducativas.php');
            exit();
        }
    }

    public function updateExperiencia()
    {
        session_start();

        $rolesPermitidos = [3];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die('Error: solicitud inválida o CSRF token no válido.');
            }

            $idExperiencia = isset($_POST['idExperiencia']) ? intval($_POST['idExperiencia']) : 0;
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
            $programa = isset($_POST['programa']) ? intval($_POST['programa']) : null;

            $errors = [];

            if ($idExperiencia <= 0)
                $errors[] = 'ID de experiencia educativa inválido.';
            if (empty($nombre))
                $errors[] = 'El campo "Nombre" es obligatorio.';
            if (empty($programa))
                $errors[] = 'El campo "Programa" es obligatorio.';

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header('Location: ' . BASE_URL . '/editarExperienciaEducativa.php');
                exit();
            }

            $resultado = $this->experienciaModel->updateExperiencia($idExperiencia, $nombre, $programa);

            if ($resultado) {
                $_SESSION['message'] = "Experiencia educativa actualizada exitosamente.";
                header('Location: ' . BASE_URL . '/administrarExperienciasEducativas.php');
                exit();
            } else {
                $_SESSION['message'] = "Error al actualizar la experiencia educativa.";
                header('Location: ' . BASE_URL . '/editarExperienciaEducativa.php');
                exit();
            }
        } else {
            header('Location: ' . BASE_URL . '/administrarExperienciasEducativas.php');
            exit();
        }
    }

    public function deleteExperiencia()
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

            $idExperiencia = isset($_POST['idExperiencia']) ? intval($_POST['idExperiencia']) : 0;

            if ($idExperiencia > 0) {
                $resultado = $this->experienciaModel->deleteExperiencia($idExperiencia);

                if ($resultado) {
                    echo json_encode(['status' => 'success', 'message' => 'Experiencia educativa eliminada con éxito.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error al eliminar la experiencia educativa.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ID de experiencia educativa inválido.']);
            }
        } else {
            header('Location: ' . BASE_URL . '/administrarExperienciasEducativas.php');
            exit();
        }
    }

}