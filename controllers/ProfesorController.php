<?php
require_once '../config/config.php';
require_once '../config/connection.php';
require_once '../models/Profesor.php';

class ProfesorController
{
    private $conn;
    private $profesorModel;

    public function __construct()
    {
        $this->conn = connectiondb();
        $this->profesorModel = new Profesor($this->conn);
    }

    public function showProfesores()
    {
        session_start();

        $rolesPermitidos = [3];
        if (!isset($_SESSION['user']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
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

        $profesores = $this->profesorModel->getProfesores();

        require_once '../views/administrarProfesores.php';
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
        $rol = 1;

        if (isset($_SESSION['errors'])) {
            $errors = $_SESSION['errors'];
            unset($_SESSION['errors']);
        }

        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
            unset($_SESSION['message']);
        }

        require_once '../views/registroProfesor.php';
    }

    public function createProfesor()
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
            $rol = isset($_POST['rol']) ? intval($_POST['rol']) : 1; // Por defecto, rol de profesor es 1

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
                header('Location: ' . BASE_URL . '/registroProfesor.php');
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

            $resultado = $this->profesorModel->createProfesor($data);

            if ($resultado) {
                $_SESSION['message'] = "Profesor registrado exitosamente.";
                header("Location: " . BASE_URL . "/administrarProfesores.php");
                exit();
            } else {
                $_SESSION['message'] = "Error al registrar el profesor.";
                header('Location: ' . BASE_URL . '/registroProfesor.php');
                exit();
            }
        } else {
            header('Location: ' . BASE_URL . '/registroProfesor.php');
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
            $idTutor = isset($_POST['idTutor']) ? intval($_POST['idTutor']) : 0;

            if ($idTutor > 0) {
                $profesor = $this->profesorModel->getProfesorById($idTutor);

                if ($profesor) {
                    if (empty($_SESSION['csrf_token'])) {
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    }

                    $user = $_SESSION['user'];
                    $csrf_token = $_SESSION['csrf_token'];

                    $rol = $profesor['rol'];
                    $regresar = BASE_URL . '/cerrarSesion.php';
                    $academico = 'Académico';
                    switch ($rol) {
                        case 1:
                            $regresar = BASE_URL . '/administrarProfesores.php';
                            $academico = 'Profesor';
                            break;
                        case 4:
                            $regresar = BASE_URL . '/administrarCoordinadores.php';
                            $academico = 'Coordinador';
                            break;
                        case 5:
                            $regresar = BASE_URL . '/administrarJefesCarrera.php';
                            $academico = 'Jefe de Carrera';
                            break;
                        default:
                            $regresar = BASE_URL . '/cerrarSesion.php';
                            break;
                    }

                    if (isset($_SESSION['errors'])) {
                        $errors = $_SESSION['errors'];
                        unset($_SESSION['errors']);
                    }

                    if (isset($_SESSION['message'])) {
                        $message = $_SESSION['message'];
                        unset($_SESSION['message']);
                    }

                    require_once '../views/editarProfesor.php';
                } else {
                    $_SESSION['message'] = 'Profesor no encontrado';
                    header("Location: " . BASE_URL . "/administrarProfesores.php");
                    exit();
                }
            } else {
                $_SESSION['message'] = 'ID de profesor inválido';
                header("Location: " . BASE_URL . "/administrarProfesores.php");
                exit();
            }
        } else {
            header("Location: " . BASE_URL . "/administrarProfesores.php");
            exit();
        }
    }

    public function updateProfesor()
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

            $idTutor = isset($_POST['idTutor']) ? intval($_POST['idTutor']) : 0;
            $rol = isset($_POST['rol']) ? intval($_POST['rol']) : 1;

            if ($idTutor <= 0) {
                $_SESSION['message'] = 'ID de profesor inválido';
                header('Location: ' . BASE_URL . '/administrar’Profesores.php');
                exit();
            }

            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $apellidoPaterno = isset($_POST['paterno']) ? trim($_POST['paterno']) : '';
            $apellidoMaterno = isset($_POST['materno']) ? trim($_POST['materno']) : '';
            $noPersonal = isset($_POST['noPersonal']) ? trim($_POST['noPersonal']) : '';
            $correoInstitucional = isset($_POST['correoInstitucional']) ? trim($_POST['correoInstitucional']) : '';

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
                $_POST['idTutor'] = $idTutor;
                header('Location: ' . BASE_URL . '/editarProfesor.php');
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

            $resultado = $this->profesorModel->updateProfesor($idTutor, $data);

            if ($resultado) {
                $_SESSION['message'] = "Profesor actualizado exitosamente.";

                if ($rol === 4) {
                    header("Location: " . BASE_URL . "/administrarCoordinadores.php");
                } elseif ($rol === 5) {
                    header("Location: " . BASE_URL . "/administrarJefesCarrera.php");
                } else {
                    header("Location: " . BASE_URL . "/administrarProfesores.php");
                }

                exit();
            } else {
                $_SESSION['message'] = "Error al actualizar el profesor.";
                header("Location: " . BASE_URL . "/editarProfesor.php");
                exit();
            }
        } else {
            header('Location: ' . BASE_URL . '/administrarProfesores.php');
            exit();
        }
    }

    public function deleteProfesor()
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

            $idTutor = isset($_POST['idTutor']) ? intval($_POST['idTutor']) : 0;

            if ($idTutor > 0) {
                $resultado = $this->profesorModel->deleteProfesor($idTutor);

                if ($resultado) {
                    echo json_encode(['status' => 'success', 'message' => 'Profesor eliminado con éxito.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el profesor.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ID de profesor inválido.']);
            }
        } else {
            header('Location: ' . BASE_URL . '/administrar’Profesores.php');
            exit();
        }
    }
}