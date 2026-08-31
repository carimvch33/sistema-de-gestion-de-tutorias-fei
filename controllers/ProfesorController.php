<?php
require_once '../config/config.php';
require_once '../config/connection.php';
require_once '../models/Profesor.php';
require_once '../models/Carrera.php';

class ProfesorController
{
    private $conn;
    private $profesorModel;
    private $carreraModel;

    public function __construct()
    {
        $this->conn = connectiondb();
        $this->profesorModel = new Profesor($this->conn);
        $this->carreraModel = new Carrera($this->conn);
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

        $carrerasTodas = $this->carreraModel->getCarreras();

        require_once '../views/registroProfesor.php';
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

                    $carrerasTodas = $this->carreraModel->getCarreras();
                    $carrerasProfesor = $this->profesorModel->getIdsCarrerasByTutor($idTutor);

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
            $rol = isset($_POST['rol']) ? intval($_POST['rol']) : 1; 
            $carreras = isset($_POST['carreras']) ? $_POST['carreras'] : []; // NUEVO

            $errors = [];

            if (empty($nombre)) $errors[] = 'El campo "Nombre" es obligatorio.';
            if (empty($correoInstitucional)) $errors[] = 'El campo "Correo institucional" es obligatorio.';

            if (!empty($correoInstitucional) && !preg_match('/^.+@(uv\.mx|estudiantes\.uv\.mx)$/', $correoInstitucional)) {
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

            $resultado = $this->profesorModel->createProfesor($data, $carreras);

            if ($resultado) {
                $_SESSION['message'] = "Profesor registrado exitosamente.";
                header("Location: " . BASE_URL . "/administrarProfesores.php");
                exit();
            } else {
                if (isset($_SESSION['message'])) {
                    $_SESSION['errors'] = [$_SESSION['message']];
                    unset($_SESSION['message']);
                } else {
                    $_SESSION['errors'] = ["Error al registrar el profesor."];
                }
                header('Location: ' . BASE_URL . '/registroProfesor.php');
                exit();
            }
        } else {
            header('Location: ' . BASE_URL . '/registroProfesor.php');
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
            $carreras = isset($_POST['carreras']) ? $_POST['carreras'] : [];

            if ($idTutor <= 0) {
                $_SESSION['message'] = 'ID de profesor inválido';
                header('Location: ' . BASE_URL . '/administrarProfesores.php');
                exit();
            }

            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $apellidoPaterno = isset($_POST['paterno']) ? trim($_POST['paterno']) : '';
            $apellidoMaterno = isset($_POST['materno']) ? trim($_POST['materno']) : '';
            $noPersonal = isset($_POST['noPersonal']) ? trim($_POST['noPersonal']) : '';
            $correoInstitucional = isset($_POST['correoInstitucional']) ? trim($_POST['correoInstitucional']) : '';

            $errors = [];

            if (empty($nombre)) $errors[] = 'El campo "Nombre" es obligatorio.';
            if (empty($correoInstitucional)) $errors[] = 'El campo "Correo institucional" es obligatorio.';

            if (!empty($correoInstitucional) && !preg_match('/^.+@(uv\.mx|estudiantes\.uv\.mx)$/', $correoInstitucional)) {
                $errors[] = 'El correo institucional debe terminar en @uv.mx o @estudiantes.uv.mx.';
            }

            if (!empty($errors)) {
                $profesor = [
                    'idTutor' => $idTutor,
                    'nombre' => $nombre,
                    'apellidoPaterno' => $apellidoPaterno,
                    'apellidoMaterno' => $apellidoMaterno,
                    'noPersonal' => $noPersonal,
                    'correoInstitucional' => $correoInstitucional,
                    'rol' => $rol
                ];
                
                $user = $_SESSION['user'];
                $csrf_token = $_SESSION['csrf_token'];
                $academico = 'Profesor';
                $regresar = BASE_URL . '/administrarProfesores.php';

                require_once '../views/editarProfesor.php';
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

            $resultado = $this->profesorModel->updateProfesor($idTutor, $data, $carreras);

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
                if (isset($_SESSION['message'])) {
                    $errors[] = $_SESSION['message'];
                    unset($_SESSION['message']);
                } else {
                    $errors[] = "Error al actualizar el profesor.";
                }

                $profesor = [
                    'idTutor' => $idTutor,
                    'nombre' => $nombre,
                    'apellidoPaterno' => $apellidoPaterno,
                    'apellidoMaterno' => $apellidoMaterno,
                    'noPersonal' => $noPersonal,
                    'correoInstitucional' => $correoInstitucional,
                    'rol' => $rol
                ];
                
                $user = $_SESSION['user'];
                $csrf_token = $_SESSION['csrf_token'];
                $academico = 'Profesor';
                $regresar = BASE_URL . '/administrarProfesores.php';

                require_once '../views/editarProfesor.php';
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

    public function importarProfesoresCSV()
    {
        session_start();
        $rolesPermitidos = [3];
        if (!isset($_SESSION['user']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $_SESSION['errors'] = ["Error: Solicitud no válida."];
                header('Location: ' . BASE_URL . '/administrarProfesores.php');
                exit();
            }

            if (isset($_FILES['csv_docentes']) && $_FILES['csv_docentes']['error'] == 0) {
                $archivoTmp = $_FILES['csv_docentes']['tmp_name'];
                
                if (($handle = fopen($archivoTmp, "r")) !== FALSE) {
                    // Saltar la línea de encabezados
                    fgetcsv($handle, 1000, ",");
                    
                    $registrados = 0;
                    $omitidos = 0;

                    // Leer línea por línea
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $maestro = isset($data[1]) ? trim($data[1]) : ''; // Nombre completo
                        $correo = isset($data[2]) ? trim($data[2]) : '';  // Correo
                        
                        if (!empty($maestro) && !empty($correo)) {
                            // Validar formato de correo UV
                            if (!preg_match('/^.+@(uv\.mx|estudiantes\.uv\.mx)$/', $correo)) {
                                $omitidos++;
                                continue;
                            }

                            // Comprobar si el correo ya existe en la tabla sesion para evitar duplicados
                            $check = $this->conn->prepare("SELECT idSesion FROM sesion WHERE correoInstitucional = ? LIMIT 1");
                            $check->bind_param("s", $correo);
                            $check->execute();
                            $check->store_result();

                            if ($check->num_rows > 0) {
                                // Ya existe, omitimos
                                $omitidos++;
                                $check->close();
                            } else {
                                $check->close();
                                
                                // 1. Insertamos primero en la tabla sesion (rol 1 = Tutor)
                                $stmtSesion = $this->conn->prepare("INSERT INTO sesion (correoInstitucional, rol) VALUES (?, 1)");
                                $stmtSesion->bind_param("s", $correo);
                                
                                if ($stmtSesion->execute()) {
                                    $idSesion = $this->conn->insert_id;
                                    $stmtSesion->close();
                                    
                                    // 2. Insertamos en la tabla tutor vinculando el idSesion
                                    $stmtTutor = $this->conn->prepare("INSERT INTO tutor (nombre, apellidoPaterno, apellidoMaterno, noPersonal, correoInstitucional, sesion) VALUES (?, '', '', '', ?, ?)");
                                    $stmtTutor->bind_param("ssi", $maestro, $correo, $idSesion);
                                    
                                    if ($stmtTutor->execute()) {
                                        $registrados++;
                                    } else {
                                        $omitidos++;
                                    }
                                    $stmtTutor->close();
                                } else {
                                    $omitidos++;
                                    $stmtSesion->close();
                                }
                            }
                        }
                    }
                    fclose($handle);

                    $_SESSION['message'] = "Importación finalizada. Registrados exitosamente: $registrados. Omitidos/Duplicados: $omitidos.";
                    header('Location: ' . BASE_URL . '/administrarProfesores.php');
                    exit();
                } else {
                    $_SESSION['errors'] = ["No se pudo leer el archivo CSV."];
                }
            } else {
                $_SESSION['errors'] = ["Por favor, selecciona un archivo CSV válido."];
            }
            
            header('Location: ' . BASE_URL . '/administrarProfesores.php');
            exit();
        }
    }
}