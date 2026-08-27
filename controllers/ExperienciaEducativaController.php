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

        // Cargar modelos necesarios para las listas desplegables
        require_once '../models/PeriodoEscolar.php';
        $periodoModel = new PeriodoEscolar($this->conn);
        
        $programas = $this->carreraModel->getCarreras();
        $periodos = $periodoModel->getPeriodos();
        $experiencias = $this->experienciaModel->getExperiencias();

        require_once '../views/administrarExperienciasEducativas.php';
    }

    public function importarExperienciasCSV()
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
                header('Location: ' . BASE_URL . '/administrarExperienciasEducativas.php');
                exit();
            }

            $idCarrera = isset($_POST['idCarrera']) ? intval($_POST['idCarrera']) : 0;
            $idPeriodo = isset($_POST['idPeriodo']) ? intval($_POST['idPeriodo']) : 0;

            if ($idCarrera === 0 || $idPeriodo === 0) {
                $_SESSION['errors'] = ["Por favor, selecciona una Carrera y un Periodo."];
                header('Location: ' . BASE_URL . '/administrarExperienciasEducativas.php');
                exit();
            }

            if (isset($_FILES['csv_materias']) && $_FILES['csv_materias']['error'] == 0) {
                $archivoTmp = $_FILES['csv_materias']['tmp_name'];
                
                if (($handle = fopen($archivoTmp, "r")) !== FALSE) {
                    // Limpiar posible BOM del CSV
                    $bom = fread($handle, 3);
                    if ($bom != "\xEF\xBB\xBF") rewind($handle);
                    
                    fgetcsv($handle, 1000, ","); // Saltar línea de encabezados
                    
                    $registrados = 0;
                    $omitidos = 0;

                    // Consultas preparadas
                    $stmtFindTutor = $this->conn->prepare("SELECT idTutor FROM tutor WHERE TRIM(CONCAT(nombre, ' ', COALESCE(apellidoPaterno, ''), ' ', COALESCE(apellidoMaterno, ''))) = ? LIMIT 1");
                    $stmtFindEE = $this->conn->prepare("SELECT idExperienciaEducativa FROM experiencia_educativa WHERE nombre = ? AND programaEducativo = ? LIMIT 1");
                    $stmtInsertEE = $this->conn->prepare("INSERT INTO experiencia_educativa (nombre, programaEducativo) VALUES (?, ?)");
                    $stmtInsertSeccion = $this->conn->prepare("INSERT INTO seccion (idProfesor, idExperienciaEducativa, idPeriodo, nrc) VALUES (?, ?, ?, ?)");

                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $docente = isset($data[0]) ? trim(preg_replace('/\s+/', ' ', $data[0])) : '';
                        $materia = isset($data[1]) ? trim($data[1]) : '';
                        $nrc = isset($data[2]) ? trim($data[2]) : '';
                        
                        if (!empty($docente) && !empty($materia) && !empty($nrc)) {
                            // 1. Buscar al maestro
                            $idTutor = null;
                            $stmtFindTutor->bind_param("s", $docente);
                            $stmtFindTutor->execute();
                            $resTutor = $stmtFindTutor->get_result();
                            if ($rowTutor = $resTutor->fetch_assoc()) {
                                $idTutor = $rowTutor['idTutor'];
                            }
                            
                            if ($idTutor === null) {
                                $omitidos++;
                                continue;
                            }

                            // 2. Buscar o crear Materia
                            $idEE = null;
                            $stmtFindEE->bind_param("si", $materia, $idCarrera);
                            $stmtFindEE->execute();
                            $resEE = $stmtFindEE->get_result();
                            
                            if ($rowEE = $resEE->fetch_assoc()) {
                                $idEE = $rowEE['idExperienciaEducativa'];
                            } else {
                                $stmtInsertEE->bind_param("si", $materia, $idCarrera);
                                if ($stmtInsertEE->execute()) {
                                    $idEE = $this->conn->insert_id;
                                } else {
                                    $omitidos++;
                                    continue;
                                }
                            }

                            // 3. Crear el Grupo
                            try {
                                $stmtInsertSeccion->bind_param("iiis", $idTutor, $idEE, $idPeriodo, $nrc);
                                if ($stmtInsertSeccion->execute()) {
                                    $registrados++;
                                } else {
                                    $omitidos++; // Error silencioso si el NRC ya existe
                                }
                            } catch (Exception $e) {
                                $omitidos++; // Protegido contra duplicidad
                            }
                        }
                    }
                    fclose($handle);

                    $_SESSION['message'] = "Importación finalizada. Grupos asignados exitosamente: $registrados. Omitidos (Profesor no encontrado o NRC duplicado): $omitidos.";
                } else {
                    $_SESSION['errors'] = ["No se pudo leer el archivo CSV."];
                }
            } else {
                $_SESSION['errors'] = ["Por favor, selecciona un archivo CSV válido."];
            }
            
            header('Location: ' . BASE_URL . '/administrarExperienciasEducativas.php');
            exit();
        }
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

        require_once '../models/Profesor.php';
        require_once '../models/PeriodoEscolar.php';
        $profesorModel = new Profesor($this->conn);
        $periodoModel = new PeriodoEscolar($this->conn);
        
        $profesores = $profesorModel->getTutors();
        $periodos = $periodoModel->getPeriodos();

        if (isset($_SESSION['errors'])) { $errors = $_SESSION['errors']; unset($_SESSION['errors']); }
        if (isset($_SESSION['message'])) { $message = $_SESSION['message']; unset($_SESSION['message']); }

        require_once '../views/registroExperienciaEducativa.php';
    }

    public function showEditForm()
    {
        session_start();
        $rolesPermitidos = [3];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) { header('Location: ' . BASE_URL . '/cerrarSesion.php'); exit(); }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idExperiencia = isset($_POST['idExperiencia']) ? intval($_POST['idExperiencia']) : 0;

            if ($idExperiencia > 0) {
                $experiencia = $this->experienciaModel->getExperienciaIntegralById($idExperiencia);

                if ($experiencia) {
                    $_SESSION['experiencia'] = $experiencia;
                    if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); }
                    $user = $_SESSION['user'];
                    $csrf_token = $_SESSION['csrf_token'];

                    $programas = $this->carreraModel->getCarreras();
                    
                    require_once '../models/Profesor.php';
                    require_once '../models/PeriodoEscolar.php';
                    $profesorModel = new Profesor($this->conn);
                    $periodoModel = new PeriodoEscolar($this->conn);
                    $profesores = $profesorModel->getTutors();
                    $periodos = $periodoModel->getPeriodos();

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
            $nrc = isset($_POST['nrc']) ? trim($_POST['nrc']) : null;
            $idProfesor = isset($_POST['idProfesor']) && $_POST['idProfesor'] !== "" ? intval($_POST['idProfesor']) : null;
            $idPeriodo = isset($_POST['idPeriodo']) && $_POST['idPeriodo'] !== "" ? intval($_POST['idPeriodo']) : null;

            $errors = [];
            
            if (empty($nombre)) $errors[] = 'El campo "Nombre de experiencia educativa" es obligatorio.';
            if (empty($programa)) $errors[] = 'El campo "Programa educativo" es obligatorio.';
            if (empty($nrc)) $errors[] = 'El campo "NRC de la Sección" es obligatorio.';
            if (empty($idProfesor)) $errors[] = 'El campo "Profesor" es obligatorio.';
            if (empty($idPeriodo)) $errors[] = 'El campo "Periodo" es obligatorio.';

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header('Location: ' . BASE_URL . '/registroExperienciaEducativa.php');
                exit();
            }

            $result = $this->experienciaModel->createExperienciaIntegral($nombre, $programa, $nrc, $idProfesor, $idPeriodo);

            if ($result) {
                $_SESSION['message'] = "Materia y asignación registradas correctamente.";
                header('Location: ' . BASE_URL . '/administrarExperienciasEducativas.php');
                exit();
            } else {
                if (!isset($_SESSION['message'])) $_SESSION['errors'] = ["Error al procesar el registro."];
                header("Location: " . BASE_URL . "/registroExperienciaEducativa.php");
                exit();
            }
        }
    }

    public function updateExperiencia()
    {
        session_start();
        $rolesPermitidos = [3];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) { header('Location: ' . BASE_URL . '/cerrarSesion.php'); exit(); }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) { die('Error: solicitud inválida.'); }

            $idExperiencia = isset($_POST['idExperiencia']) ? intval($_POST['idExperiencia']) : 0;
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
            $programa = isset($_POST['programa']) ? intval($_POST['programa']) : null;
            
            $idSeccion = isset($_POST['idSeccion']) ? intval($_POST['idSeccion']) : null;
            $nrc = isset($_POST['nrc']) ? trim($_POST['nrc']) : null;
            $idProfesor = isset($_POST['idProfesor']) ? intval($_POST['idProfesor']) : null;
            $idPeriodo = isset($_POST['idPeriodo']) ? intval($_POST['idPeriodo']) : null;

            $errors = [];
            if ($idExperiencia <= 0) $errors[] = 'ID inválido.';
            if (empty($nombre)) $errors[] = 'El campo "Nombre" es obligatorio.';
            if (empty($programa)) $errors[] = 'El campo "Programa" es obligatorio.';

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header('Location: ' . BASE_URL . '/administrarExperienciasEducativas.php');
                exit();
            }

            $resultado = $this->experienciaModel->updateExperienciaIntegral($idExperiencia, $nombre, $programa, $idSeccion, $nrc, $idProfesor, $idPeriodo);

            if ($resultado) {
                $_SESSION['message'] = "Registro actualizado exitosamente.";
            } else {
                if (!isset($_SESSION['message'])) $_SESSION['errors'][] = "Error al actualizar.";
            }
            header('Location: ' . BASE_URL . '/administrarExperienciasEducativas.php');
            exit();
        }
    }

    public function deleteExperiencia()
    {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) { echo json_encode(['status' => 'error', 'message' => 'Token inválido.']); exit(); }
            $rolesPermitidos = [3];
            if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) { echo json_encode(['status' => 'error', 'message' => 'Acceso denegado.']); exit(); }

            $idExperiencia = isset($_POST['idExperiencia']) ? intval($_POST['idExperiencia']) : 0;

            if ($idExperiencia > 0) {
                // AHORA ELIMINA MATERIA Y SECCIÓN DE UN GOLPE
                $resultado = $this->experienciaModel->deleteExperienciaIntegral($idExperiencia);

                if ($resultado) {
                    echo json_encode(['status' => 'success', 'message' => 'Registro eliminado con éxito.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error al eliminar.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ID inválido.']);
            }
        }
    }
}