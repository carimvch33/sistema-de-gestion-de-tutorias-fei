<?php
require_once '../config/config.php';
require_once '../config/connection.php';
require_once '../models/Coordinador.php';

class CoordinadorController
{
    private $conn;
    private $coordinadorModel;

    public function __construct()
    {
        $this->conn = connectiondb();
        $this->coordinadorModel = new Coordinador($this->conn);
    }

    public function showCoordinadores()
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

        $coordinadores = $this->coordinadorModel->getCoordinadores();

        require_once '../views/administrarCoordinadores.php';
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
        $rol = 4;

        require_once '../models/Carrera.php';
        $carreraModel = new Carrera($this->conn);
        $carreras = $carreraModel->getCarreras();

        if (isset($_SESSION['errors'])) {
            $errors = $_SESSION['errors'];
            unset($_SESSION['errors']);
        }

        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
            unset($_SESSION['message']);
        }

        require_once '../views/registroCoordinador.php';
    }

    public function createCoordinador()
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
            $rol = isset($_POST['rol']) ? intval($_POST['rol']) : 4; // Rol de coordinador
            $carrerasSeleccionadas = isset($_POST['carreras']) ? $_POST['carreras'] : [];

            $errors = [];

            if (empty($nombre))
                $errors[] = 'El campo "Nombre" es obligatorio.';
            if (empty($correoInstitucional))
                $errors[] = 'El campo "Correo institucional" es obligatorio.';
            if (empty($carrerasSeleccionadas))
                $errors[] = 'Debe seleccionar al menos una carrera.';

            if (
                !empty($correoInstitucional) &&
                !preg_match('/^.+@(uv\.mx|estudiantes\.uv\.mx)$/', $correoInstitucional)
            ) {
                $errors[] = 'El correo institucional debe terminar en @uv.mx o @estudiantes.uv.mx.';
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header('Location: ' . BASE_URL . '/registroCoordinador.php');
                exit();
            }

            $data = [
                'nombre' => $nombre,
                'apellidoPaterno' => $apellidoPaterno,
                'apellidoMaterno' => $apellidoMaterno,
                'noPersonal' => $noPersonal,
                'correoInstitucional' => $correoInstitucional,
                'rol' => $rol,
                'carreras' => $carrerasSeleccionadas // Incluimos las carreras seleccionadas
            ];

            $resultado = $this->coordinadorModel->createCoordinador($data);

            if ($resultado) {
                $_SESSION['message'] = "Coordinador registrado exitosamente.";
                header('Location: ' . BASE_URL . '/administrarCoordinadores.php');
                exit();
            } else {
                $_SESSION['message'] = "Error al registrar el coordinador.";
                header("Location: " . BASE_URL . "/registroCoordinador.php");
                exit();
            }
        } else {
            header('Location: ' . BASE_URL . '/registroCoordinador.php');
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
                $coordinador = $this->coordinadorModel->getCoordinadorById($idTutor);

                if ($coordinador) {
                    if (empty($_SESSION['csrf_token'])) {
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    }

                    require_once '../models/Carrera.php';
                    $carreraModel = new Carrera($this->conn);
                    $carreras = $carreraModel->getCarreras();


                    $user = $_SESSION['user'];
                    $csrf_token = $_SESSION['csrf_token'];

                    $rol = $coordinador['rol'];
                    $regresar = BASE_URL . '/cerrarSesion.php';
                    $regresar = BASE_URL . '/administrarCoordinadores.php';
                    $academico = 'Coordinador';


                    if (isset($_SESSION['errors'])) {
                        $errors = $_SESSION['errors'];
                        unset($_SESSION['errors']);
                    }

                    if (isset($_SESSION['message'])) {
                        $message = $_SESSION['message'];
                        unset($_SESSION['message']);
                    }

                    require_once '../views/editarCoordinador.php';
                } else {
                    $_SESSION['message'] = 'Profesor no encontrado';
                    header('Location: ' . BASE_URL . '/administrarCoordinadores.php');
                    exit();
                }
            } else {
                $_SESSION['message'] = 'ID de profesor inválido';
                header('Location: ' . BASE_URL . '/administrarCoordinadores.php');
                exit();
            }
        } else {
            header('Location: ' . BASE_URL . '/administrarCoordinadores.php');
            exit();
        }
    }

    public function updateCoordinador()
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
                header('Location: ' . BASE_URL . '/administrarCoordinadores.php');
                exit();
            }

            $errors = [];

            $carrerasSeleccionadas = isset($_POST['carreras']) ? $_POST['carreras'] : [];
            if (empty($carrerasSeleccionadas)) {
                $errors[] = 'Debe seleccionar al menos una carrera.';
            }

            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $apellidoPaterno = isset($_POST['paterno']) ? trim($_POST['paterno']) : '';
            $apellidoMaterno = isset($_POST['materno']) ? trim($_POST['materno']) : '';
            $noPersonal = isset($_POST['noPersonal']) ? trim($_POST['noPersonal']) : '';
            $correoInstitucional = isset($_POST['correoInstitucional']) ? trim($_POST['correoInstitucional']) : '';

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
                header('Location: ' . BASE_URL . '/editarCoordinador.php');
                exit();
            }

            $data = [
                'nombre' => $nombre,
                'apellidoPaterno' => $apellidoPaterno,
                'apellidoMaterno' => $apellidoMaterno,
                'noPersonal' => $noPersonal,
                'correoInstitucional' => $correoInstitucional,
                'rol' => $rol,
                'carreras' => $carrerasSeleccionadas
            ];

            $resultado = $this->coordinadorModel->updateCoordinador($idTutor, $data);

            if ($resultado) {
                $_SESSION['message'] = "Profesor actualizado exitosamente.";
                header('Location: ' . BASE_URL . '/administrarCoordinadores.php');
                exit();
            } else {
                $_SESSION['message'] = "Error al actualizar el profesor.";
                header("Location: " . BASE_URL . "/editarCoordinador.php");
                exit();
            }
        } else {
            header('Location: ' . BASE_URL . '/administrarCoordinadores.php');
            exit();
        }
    }

    public function deleteCoordinador()
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
                $resultado = $this->coordinadorModel->deleteCoordinador($idTutor);

                if ($resultado) {
                    echo json_encode(['status' => 'success', 'message' => 'Coordinador eliminado con éxito.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el coordinador.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ID de coordinador inválido.']);
            }
        } else {
            header('Location: ' . BASE_URL . '/administrarCoordinadores.php');
            exit();
        }
    }
}