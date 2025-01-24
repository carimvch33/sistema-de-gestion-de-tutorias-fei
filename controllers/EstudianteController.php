<?php
require_once '../config/config.php';
require_once '../config/connection.php';
require_once '../models/Estudiante.php';
require_once '../models/Carrera.php';
require_once '../models/Profesor.php';

class EstudianteController
{
    private $conn;
    private $estudianteModel;
    private $carreraModel;
    private $tutorModel;

    public function __construct()
    {
        $this->conn = connectiondb();
        $this->estudianteModel = new Estudiante($this->conn);
        $this->carreraModel = new Carrera($this->conn);
        $this->tutorModel = new Profesor($this->conn);
    }

    public function showEstudiantes()
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

        $estudiantes = $this->estudianteModel->getEstudiantes();

        require_once '../views/administrarEstudiantes.php';
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

        // Obtener listas de carreras y tutores
        $carreras = $this->carreraModel->getCarreras();
        $tutores = $this->tutorModel->getTutors();

        if (isset($_SESSION['errors'])) {
            $errors = $_SESSION['errors'];
            unset($_SESSION['errors']);
        }

        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
            unset($_SESSION['message']);
        }

        require_once '../views/registroEstudiante.php';
    }

    public function createEstudiante()
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

            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $apellidoPaterno = isset($_POST['paterno']) ? trim($_POST['paterno']) : '';
            $apellidoMaterno = isset($_POST['materno']) ? trim($_POST['materno']) : '';
            $matricula = isset($_POST['matricula']) ? trim($_POST['matricula']) : '';
            $carrera = isset($_POST['carrera']) ? intval($_POST['carrera']) : 0;
            $correoInstitucional = isset($_POST['correoInstitucional']) ? trim($_POST['correoInstitucional']) : '';
            $tutor = isset($_POST['tutor']) ? intval($_POST['tutor']) : null;
            $rol = 2;

            $errors = [];

            if (empty($nombre))
                $errors[] = 'El campo "Nombre" es obligatorio.';
            if (empty($matricula))
                $errors[] = 'El campo "Matrícula" es obligatorio.';
            if ($carrera <= 0)
                $errors[] = 'El campo "Carrera" es obligatorio.';
            if (empty($correoInstitucional))
                $errors[] = 'El campo "Correo institucional" es obligatorio.';

            if (!empty($matricula) && (!preg_match('/^S\d{8}$/', $matricula))) {
                $errors[] = 'La matrícula debe comenzar con "S" seguido de 8 dígitos.';
            }

            if (!empty($correoInstitucional) && (!preg_match("/^z{$matricula}@estudiantes\.uv\.mx$/i", $correoInstitucional))) {
                $errors[] = 'El correo institucional debe ser "zMATRÍCULA@estudiantes.uv.mx".';
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header('Location: registroEstudiante.php');
                exit();
            }

            $data = [
                'nombre' => $nombre,
                'apellidoPaterno' => $apellidoPaterno,
                'apellidoMaterno' => $apellidoMaterno,
                'matricula' => $matricula,
                'carrera' => $carrera,
                'correoInstitucional' => $correoInstitucional,
                'tutor' => $tutor,
                'rol' => $rol
            ];

            $resultado = $this->estudianteModel->createEstudiante($data);

            if ($resultado) {
                $_SESSION['message'] = "Estudiante registrado exitosamente.";
                header('Location: ' . BASE_URL . '/administrarEstudiantes.php');
                exit();
            } else {
                $_SESSION['message'] = "Error al registrar el estudiante.";
                header("Location: " . BASE_URL . "/registroEstudiante.php");
                exit();
            }
        } else {
            header('Location: ' . BASE_URL . '/registroEstudiante.php');
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
            $idTutorado = isset($_POST['idTutorado']) ? intval($_POST['idTutorado']) : 0;

            if ($idTutorado > 0) {
                $estudiante = $this->estudianteModel->getEstudianteById($idTutorado);

                if ($estudiante) {
                    if (empty($_SESSION['csrf_token'])) {
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    }

                    $user = $_SESSION['user'];
                    $csrf_token = $_SESSION['csrf_token'];

                    // Obtener listas de carreras y tutores
                    $carreras = $this->carreraModel->getCarreras();
                    $tutores = $this->tutorModel->getTutors();

                    if (isset($_SESSION['errors'])) {
                        $errors = $_SESSION['errors'];
                        unset($_SESSION['errors']);
                    }

                    if (isset($_SESSION['message'])) {
                        $message = $_SESSION['message'];
                        unset($_SESSION['message']);
                    }

                    require_once '../views/editarEstudiante.php';
                } else {
                    $_SESSION['message'] = 'Estudiante no encontrado';
                    header('Location: ' . BASE_URL . '/administrarEstudiantes.php');
                    exit();
                }
            } else {
                $_SESSION['message'] = 'ID de estudiante inválido';
                header('Location: ' . BASE_URL . '/administrarEstudiantes.php');
                exit();
            }
        } else {
            header('Location: ' . BASE_URL . '/administrarEstudiantes.php');
            exit();
        }
    }

    public function updateEstudiante()
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

            $idTutorado = isset($_POST['idTutorado']) ? intval($_POST['idTutorado']) : 0;

            if ($idTutorado <= 0) {
                $_SESSION['message'] = 'ID de estudiante inválido';
                header('Location: ' . BASE_URL . '/administrarEstudiantes.php');
                exit();
            }

            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $apellidoPaterno = isset($_POST['paterno']) ? trim($_POST['paterno']) : '';
            $apellidoMaterno = isset($_POST['materno']) ? trim($_POST['materno']) : '';
            $matricula = isset($_POST['matricula']) ? trim($_POST['matricula']) : '';
            $carrera = isset($_POST['carrera']) ? intval($_POST['carrera']) : 0;
            $correoInstitucional = isset($_POST['correoInstitucional']) ? trim($_POST['correoInstitucional']) : '';
            $tutor = isset($_POST['tutor']) ? intval($_POST['tutor']) : null;

            $errors = [];

            if (empty($nombre))
                $errors[] = 'El campo "Nombre" es obligatorio.';
            if (empty($matricula))
                $errors[] = 'El campo "Matrícula" es obligatorio.';
            if ($carrera <= 0)
                $errors[] = 'El campo "Carrera" es obligatorio.';
            if (empty($correoInstitucional))
                $errors[] = 'El campo "Correo institucional" es obligatorio.';

            if (!empty($matricula) && (!preg_match('/^S\d{8}$/', $matricula))) {
                $errors[] = 'La matrícula debe comenzar con "S" seguido de 8 dígitos.';
            }

            if (!empty($correoInstitucional) && (!preg_match("/^z{$matricula}@estudiantes\.uv\.mx$/i", $correoInstitucional))) {
                $errors[] = 'El correo institucional debe ser "zMATRÍCULA@estudiantes.uv.mx".';
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_POST['idTutorado'] = $idTutorado;
                header('Location: ' . BASE_URL . '/editarEstudiante.php');
                exit();
            }

            $data = [
                'nombre' => $nombre,
                'apellidoPaterno' => $apellidoPaterno,
                'apellidoMaterno' => $apellidoMaterno,
                'matricula' => $matricula,
                'carrera' => $carrera,
                'correoInstitucional' => $correoInstitucional,
                'tutor' => $tutor
            ];

            $resultado = $this->estudianteModel->updateEstudiante($idTutorado, $data);

            if ($resultado) {
                $_SESSION['message'] = "Estudiante actualizado exitosamente.";
                header('Location: ' . BASE_URL . '/administrarEstudiantes.php');
                exit();
            } else {
                $_SESSION['message'] = "Error al actualizar el estudiante.";
                header("Location: " . BASE_URL . "/editarEstudiante.php");
                exit();
            }
        } else {
            header('Location: ' . BASE_URL . '/administrarEstudiantes.php');
            exit();
        }
    }

    public function deleteEstudiante()
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

            $idTutorado = isset($_POST['idTutorado']) ? intval($_POST['idTutorado']) : 0;

            if ($idTutorado > 0) {
                $resultado = $this->estudianteModel->deleteEstudiante($idTutorado);

                if ($resultado) {
                    echo json_encode(['status' => 'success', 'message' => 'Estudiante eliminado con éxito.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el estudiante.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ID de estudiante inválido.']);
            }
        } else {
            header('Location: ' . BASE_URL . '/administrarEstudiantes.php');
            exit();
        }
    }
}