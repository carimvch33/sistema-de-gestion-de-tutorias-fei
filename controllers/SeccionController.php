<?php
require_once '../config/config.php';
require_once '../config/connection.php';
require_once '../models/Seccion.php';

class SeccionController
{
    private $conn;
    private $seccionModel;

    public function __construct()
    {
        $this->conn = connectiondb();
        $this->seccionModel = new Seccion($this->conn);
    }

    public function showSecciones()
    {
        session_start();

        $rolesPermitidos = [3]; // Ajusta los roles según sea necesario
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        // CSRF Token
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $user = $_SESSION['user'];
        $csrf_token = $_SESSION['csrf_token'];

        $secciones = $this->seccionModel->getSecciones();

        require_once '../views/administrarSecciones.php';
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

        if (isset($_SESSION['errors'])) {
            $errors = $_SESSION['errors'];
            unset($_SESSION['errors']);
        }

        require_once '../models/Profesor.php';
        require_once '../models/ExperienciaEducativa.php';
        require_once '../models/PeriodoEscolar.php';

        $profesorModel = new Profesor($this->conn);
        $experienciaModel = new ExperienciaEducativa($this->conn);
        $periodoModel = new PeriodoEscolar($this->conn);

        $profesores = $profesorModel->getTutors();
        $experiencias = $experienciaModel->getExperiencias();
        $periodos = $periodoModel->getPeriodos();

        require_once '../views/registroSeccion.php';
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
            $idSeccion = isset($_POST['idSeccion']) ? intval($_POST['idSeccion']) : 0;

            if ($idSeccion > 0) {
                $seccion = $this->seccionModel->getSeccionById($idSeccion);

                if ($seccion) {
                    $_SESSION['seccion'] = $seccion;

                    if (empty($_SESSION['csrf_token'])) {
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    }

                    $csrf_token = $_SESSION['csrf_token'];
                    $user = $_SESSION['user'];

                    // Obtener datos para los select inputs
                    require_once '../models/Profesor.php';
                    require_once '../models/ExperienciaEducativa.php';
                    require_once '../models/PeriodoEscolar.php';

                    $profesorModel = new Profesor($this->conn);
                    $experienciaModel = new ExperienciaEducativa($this->conn);
                    $periodoModel = new PeriodoEscolar($this->conn);

                    $profesores = $profesorModel->getProfesores();
                    $experiencias = $experienciaModel->getExperiencias();
                    $periodos = $periodoModel->getPeriodos();

                    require_once '../views/editarSeccion.php';
                } else {
                    $_SESSION['message'] = 'Sección no encontrada';
                    header('Location: ' . BASE_URL . '/administrarSecciones.php');
                    exit();
                }
            } else {
                $_SESSION['message'] = 'ID de sección inválido';
                header('Location: ' . BASE_URL . '/administrarSecciones.php');
                exit();
            }
        } else {
            header('Location: ' . BASE_URL . '/administrarSecciones.php');
            exit();
        }
    }

    public function createSeccion()
    {
        session_start();

        $rolesPermitidos = [3];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $_SESSION['message'] = "Error: Solicitud no válida.";
                header('Location: ' . BASE_URL . '/registroSeccion.php');
                exit();
            }

            $nrc = isset($_POST['nrc']) ? trim($_POST['nrc']) : null;
            $idProfesor = isset($_POST['idProfesor']) ? intval($_POST['idProfesor']) : null;
            $idExperienciaEducativa = isset($_POST['idExperienciaEducativa']) ? intval($_POST['idExperienciaEducativa']) : null;
            $idPeriodo = isset($_POST['idPeriodo']) ? intval($_POST['idPeriodo']) : null;

            $errors = [];

            if (empty($nrc)) {
                $errors[] = 'El campo "NRC" es obligatorio.';
            }
            if (empty($idProfesor)) {
                $errors[] = 'El campo "Profesor" es obligatorio.';
            }
            if (empty($idExperienciaEducativa)) {
                $errors[] = 'El campo "Experiencia Educativa" es obligatorio.';
            }
            if (empty($idPeriodo)) {
                $errors[] = 'El campo "Periodo" es obligatorio.';
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header('Location: ' . BASE_URL . '/registroSeccion.php');
                exit();
            }

            $resultado = $this->seccionModel->createSeccion($idProfesor, $idExperienciaEducativa, $idPeriodo, $nrc);

            if ($resultado) {
                $_SESSION['message'] = "Sección registrada exitosamente.";
                header('Location: ' . BASE_URL . '/administrarSecciones.php');
                exit();
            } else {
                if (isset($_SESSION['message'])) {
                    $_SESSION['errors'] = [$_SESSION['message']];
                    unset($_SESSION['message']);
                } else {
                    $_SESSION['errors'] = ["Error al registrar la sección."];
                }
                
                header('Location: ' . BASE_URL . "/registroSeccion.php");
                exit();
            }
        } else {
            header('Location: ' . BASE_URL . '/registroSeccion.php');
            exit();
        }
    }

    public function updateSeccion()
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

            $idSeccion = isset($_POST['idSeccion']) ? intval($_POST['idSeccion']) : 0;
            $nrc = isset($_POST['nrc']) ? trim($_POST['nrc']) : null;
            $idProfesor = isset($_POST['idProfesor']) ? intval($_POST['idProfesor']) : null;
            $idExperienciaEducativa = isset($_POST['idExperienciaEducativa']) ? intval($_POST['idExperienciaEducativa']) : null;
            $idPeriodo = isset($_POST['idPeriodo']) ? intval($_POST['idPeriodo']) : null;

            $errors = [];

            if ($idSeccion <= 0) {
                $errors[] = 'ID de sección inválido.';
            }
            if (empty($nrc)) {
                $errors[] = 'El campo "NRC" es obligatorio.';
            }
            if (empty($idProfesor)) {
                $errors[] = 'El campo "Profesor" es obligatorio.';
            }
            if (empty($idExperienciaEducativa)) {
                $errors[] = 'El campo "Experiencia Educativa" es obligatorio.';
            }
            if (empty($idPeriodo)) {
                $errors[] = 'El campo "Periodo" es obligatorio.';
            }

            $recargarVista = function() use ($idSeccion, $nrc, $idProfesor, $idExperienciaEducativa, $idPeriodo) {
                $_SESSION['seccion'] = [
                    'id' => $idSeccion, 
                    'nrc' => $nrc, 
                    'idProfesor' => $idProfesor, 
                    'idExperienciaEducativa' => $idExperienciaEducativa, 
                    'idPeriodo' => $idPeriodo
                ];
                $user = $_SESSION['user'];
                $csrf_token = $_SESSION['csrf_token'];

                require_once '../models/Profesor.php';
                require_once '../models/ExperienciaEducativa.php';
                require_once '../models/PeriodoEscolar.php';

                $profesorModel = new Profesor($this->conn);
                $experienciaModel = new ExperienciaEducativa($this->conn);
                $periodoModel = new PeriodoEscolar($this->conn);

                $profesores = $profesorModel->getProfesores();
                $experiencias = $experienciaModel->getExperiencias();
                $periodos = $periodoModel->getPeriodos();

                require_once '../views/editarSeccion.php';
                exit();
            };

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $recargarVista();
            }

            $resultado = $this->seccionModel->updateSeccion($idSeccion, $idProfesor, $idExperienciaEducativa, $idPeriodo, $nrc);

            if ($resultado) {
                $_SESSION['message'] = "Sección actualizada exitosamente.";
                header('Location: ' . BASE_URL . '/administrarSecciones.php');
                exit();
            } else {
                if (isset($_SESSION['message'])) {
                    $_SESSION['errors'][] = $_SESSION['message'];
                    unset($_SESSION['message']);
                } else {
                    $_SESSION['errors'][] = "Error al actualizar la sección.";
                }
                
                $recargarVista();
            }
        } else {
            header('Location: ' . BASE_URL . '/administrarSecciones.php');
            exit();
        }
    }

    public function deleteSeccion()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verificar token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                echo json_encode(['status' => 'error', 'message' => 'Token CSRF inválido.']);
                exit();
            }

            // Verificar permisos
            $rolesPermitidos = [3];
            if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
                echo json_encode(['status' => 'error', 'message' => 'Acceso denegado.']);
                exit();
            }

            // Obtener el ID de la sección
            $idSeccion = isset($_POST['idSeccion']) ? intval($_POST['idSeccion']) : 0;

            if ($idSeccion > 0) {
                $resultado = $this->seccionModel->deleteSeccion($idSeccion);

                if ($resultado) {
                    echo json_encode(['status' => 'success', 'message' => 'Sección eliminada con éxito.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error al eliminar la sección.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ID de sección inválido.']);
            }
        } else {
            header('Location: ' . BASE_URL . '/administrarSecciones.php');
            exit();
        }
    }
}
?>