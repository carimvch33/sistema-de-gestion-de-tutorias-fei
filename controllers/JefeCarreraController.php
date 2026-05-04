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
        $rol = 5; 

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
            
            $carrerasSeleccionadas = isset($_POST['carreras']) ? $_POST['carreras'] : [];
            $rol = 5; 

            $errors = [];

            if (empty($nombre)) $errors[] = 'El campo "Nombre" es obligatorio.';
            if (empty($correoInstitucional)) $errors[] = 'El campo "Correo institucional" es obligatorio.';
            if (empty($carrerasSeleccionadas)) $errors[] = 'Debe asignar al menos una carrera al Jefe de Carrera.';

            if (!empty($correoInstitucional) && !preg_match('/^.+@(uv\.mx|estudiantes\.uv\.mx)$/', $correoInstitucional)) {
                $errors[] = 'El correo institucional debe terminar en @uv.mx o @estudiantes.uv.mx.';
            }

            if (!empty($carrerasSeleccionadas) && $this->jefeCarreraModel->isCarreraAsignada($carrerasSeleccionadas)) {
                $errors[] = 'Una o más de las carreras seleccionadas ya tienen un Jefe asignado.';
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
                'carreras' => $carrerasSeleccionadas, 
                'rol' => $rol
            ];

            $resultado = $this->jefeCarreraModel->createJefeCarrera($data);

            if ($resultado) {
                $_SESSION['message'] = "Jefe de carrera registrado exitosamente.";
                header("Location: " . BASE_URL . "/administrarJefesCarrera.php");
                exit();
            } else {
                if (isset($_SESSION['message'])) {
                    $_SESSION['errors'] = [$_SESSION['message']];
                    unset($_SESSION['message']);
                } else {
                    $_SESSION['errors'] = ["Error al registrar el jefe de carrera."];
                }
                header("Location: " . BASE_URL . "/registroJefeCarrera.php");
                exit();
            }
        } else {
            header("Location: " . BASE_URL . "/registroJefeCarrera.php");
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
                $jefeCarrera = $this->jefeCarreraModel->getJefeCarreraById($idTutor);

                if ($jefeCarrera) {
                    if (empty($_SESSION['csrf_token'])) {
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    }

                    $user = $_SESSION['user'];
                    $csrf_token = $_SESSION['csrf_token'];
                    
                    require_once '../models/Carrera.php';
                    $carreraModel = new Carrera($this->conn);
                    $carreras = $carreraModel->getCarreras();

                    if (isset($_SESSION['errors'])) {
                        $errors = $_SESSION['errors'];
                        unset($_SESSION['errors']);
                    }

                    require_once '../views/editarJefeCarrera.php';
                } else {
                    $_SESSION['message'] = 'Jefe de Carrera no encontrado';
                    header('Location: ' . BASE_URL . '/administrarJefesCarrera.php');
                    exit();
                }
            } else {
                $_SESSION['message'] = 'ID inválido';
                header('Location: ' . BASE_URL . '/administrarJefesCarrera.php');
                exit();
            }
        } else {
            header('Location: ' . BASE_URL . '/administrarJefesCarrera.php');
            exit();
        }
    }

    public function updateJefeCarrera()
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

            if ($idTutor <= 0) {
                $_SESSION['message'] = 'ID de jefe de carrera inválido';
                header('Location: ' . BASE_URL . '/administrarJefesCarrera.php');
                exit();
            }

            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $apellidoPaterno = isset($_POST['paterno']) ? trim($_POST['paterno']) : '';
            $apellidoMaterno = isset($_POST['materno']) ? trim($_POST['materno']) : '';
            $noPersonal = isset($_POST['noPersonal']) ? trim($_POST['noPersonal']) : '';
            $correoInstitucional = isset($_POST['correoInstitucional']) ? trim($_POST['correoInstitucional']) : '';
            
            $carrerasSeleccionadas = isset($_POST['carreras']) ? $_POST['carreras'] : [];

            $errors = [];

            if (empty($nombre)) $errors[] = 'El campo "Nombre" es obligatorio.';
            if (empty($correoInstitucional)) $errors[] = 'El campo "Correo institucional" es obligatorio.';
            if (empty($carrerasSeleccionadas)) $errors[] = 'Debe asignar al menos una carrera al Jefe de Carrera.';

            if (!empty($correoInstitucional) && !preg_match('/^.+@(uv\.mx|estudiantes\.uv\.mx)$/', $correoInstitucional)) {
                $errors[] = 'El correo institucional debe terminar en @uv.mx o @estudiantes.uv.mx.';
            }

            if (!empty($carrerasSeleccionadas) && $this->jefeCarreraModel->isCarreraAsignada($carrerasSeleccionadas, $idTutor)) {
                $errors[] = 'Una o más de las carreras seleccionadas ya tienen un Jefe asignado.';
            }

            if (!empty($errors)) {
                $jefeCarrera = [
                    'idTutor' => $idTutor,
                    'nombre' => $nombre,
                    'apellidoPaterno' => $apellidoPaterno,
                    'apellidoMaterno' => $apellidoMaterno,
                    'noPersonal' => $noPersonal,
                    'correoInstitucional' => $correoInstitucional,
                    'carreras' => $carrerasSeleccionadas
                ];
                
                $user = $_SESSION['user'];
                $csrf_token = $_SESSION['csrf_token'];

                require_once '../models/Carrera.php';
                $carreraModel = new Carrera($this->conn);
                $carreras = $carreraModel->getCarreras();

                require_once '../views/editarJefeCarrera.php';
                exit();
            }

            $data = [
                'nombre' => $nombre,
                'apellidoPaterno' => $apellidoPaterno,
                'apellidoMaterno' => $apellidoMaterno,
                'noPersonal' => $noPersonal,
                'correoInstitucional' => $correoInstitucional,
                'carreras' => $carrerasSeleccionadas
            ];

            $resultado = $this->jefeCarreraModel->updateJefeCarrera($idTutor, $data);

            if ($resultado) {
                $_SESSION['message'] = "Jefe de carrera actualizado exitosamente.";
                header('Location: ' . BASE_URL . '/administrarJefesCarrera.php');
                exit();
            } else {
                if (isset($_SESSION['message'])) {
                    $errors[] = $_SESSION['message'];
                    unset($_SESSION['message']);
                } else {
                    $errors[] = "Error al actualizar el jefe de carrera.";
                }

                $jefeCarrera = [
                    'idTutor' => $idTutor,
                    'nombre' => $nombre,
                    'apellidoPaterno' => $apellidoPaterno,
                    'apellidoMaterno' => $apellidoMaterno,
                    'noPersonal' => $noPersonal,
                    'correoInstitucional' => $correoInstitucional,
                    'carreras' => $carrerasSeleccionadas
                ];
                
                $user = $_SESSION['user'];
                $csrf_token = $_SESSION['csrf_token'];

                require_once '../models/Carrera.php';
                $carreraModel = new Carrera($this->conn);
                $carreras = $carreraModel->getCarreras();

                require_once '../views/editarJefeCarrera.php';
                exit();
            }
        } else {
            header('Location: ' . BASE_URL . '/administrarJefesCarrera.php');
            exit();
        }
    }

    public function deleteJefeCarrera()
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
                $resultado = $this->jefeCarreraModel->deleteJefeCarrera($idTutor);

                if ($resultado) {
                    echo json_encode(['status' => 'success', 'message' => 'Jefe de Carrera eliminado con éxito.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el Jefe de Carrera.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ID inválido.']);
            }
        } else {
            header('Location: ' . BASE_URL . '/administrarJefesCarrera.php');
            exit();
        }
    }
}