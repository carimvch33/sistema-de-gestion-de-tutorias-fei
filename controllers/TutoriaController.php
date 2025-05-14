<?php
require_once '../config/config.php';
require_once '../config/connection.php';
require_once '../models/Tutoria.php';
require_once '../models/Carrera.php';
require_once '../models/Profesor.php';
require_once '../models/PeriodoEscolar.php';

class TutoriaController
{
    private $conn;
    private $tutoriaModel;
    private $carreraModel;
    private $periodoModel;
    private $tutorModel;

    public function __construct()
    {
        $this->conn = connectiondb();
        $this->tutoriaModel = new Tutoria($this->conn);
        $this->carreraModel = new Carrera($this->conn);
        $this->tutorModel = new Profesor($this->conn);
        $this->periodoModel = new PeriodoEscolar($this->conn);
    }

    public function showTutorias()
    {
        session_start();
        $rolesPermitidos = [1, 4];
        if (!isset($_SESSION['user']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        $correoInstitucional = $_SESSION['correoInstitucional'];
        $result = $this->tutoriaModel->getTutoriasByTutor($correoInstitucional);

        $muestraActual = true;
        $menu = BASE_URL . '/menu.php';

        require '../views/tutorias.php';
    }

    public function showTutoringHistory()
    {
        session_start();
        $rolesPermitidos = [1, 4];
        if (!isset($_SESSION['user']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        $correoInstitucional = $_SESSION['correoInstitucional'];
        $result = $this->tutoriaModel->getTutoringHistoryByTutor($correoInstitucional);

        $muestraActual = false;
        $menu = BASE_URL . '/menu.php';

        require '../views/tutorias.php';
    }

    public function showRegistroForm()
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }


        $rolesPermitidos = [1, 4];
        if (!isset($_SESSION['user']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        $user = $_SESSION['user'];
        $correoInstitucional = $_SESSION['correoInstitucional'];
        $csrf_token = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $csrf_token;

        $stmt = $this->conn->prepare("SELECT idTutor FROM tutor WHERE correoInstitucional = ?");
        $stmt->bind_param("s", $correoInstitucional);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows === 0) {
            $_SESSION['message'] = 'No se encontró el tutor.';
            header('Location: ' . BASE_URL . '/menu.php');
            exit();
        }

        $row = $result->fetch_assoc();
        $idTutor = $row['idTutor'];

        $carreras = $this->tutoriaModel->getCarrerasByTutor($idTutor);
        $periodos = $this->periodoModel->getCurrentPeriodo();

        require '../views/registrarTutoria.php';
    }

    public function registrarTutoria()
    {
        session_start();
        $rolesPermitidos = [1, 4];

        if (!isset($_SESSION['user']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/registroTutoria.php');
            exit();
        }

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['message'] = 'Token CSRF inválido';
            header('Location: ' . BASE_URL . '/registroTutoria.php');
            exit();
        }

        $errors = $this->validateTutoriaData($_POST);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: ' . BASE_URL . '/registroTutoria.php');
            exit();
        }

        $archivoNombre = null;
        if (isset($_FILES['archivo_horario']) && $_FILES['archivo_horario']['error'] === UPLOAD_ERR_OK) {
            $archivoNombre = $this->uploadFile($_FILES['archivo_horario']);
            if (!$archivoNombre) {
                $_SESSION['message'] = 'Error al subir el archivo.';
                header('Location: ' . BASE_URL . '/registroTutoria.php');
                exit();
            }
        }

        $correoInstitucional = $_SESSION['correoInstitucional'];
        $this->tutoriaModel->crearTutoria($_POST, $correoInstitucional, $archivoNombre);

        header('Location: ' . BASE_URL . '/tutorias.php');
        exit();
    }

    private function validateTutoriaData($data)
    {
        $errors = [];

        if (empty($data['carrera'])) {
            $errors[] = 'La carrera es obligatoria.';
        }
        if (empty($data['periodoTutoria'])) {
            $errors[] = 'El periodo de tutoría es obligatorio.';
        }
        if (empty($data['periodo'])) {
            $errors[] = 'El periodo escolar es obligatorio.';
        }
        if (empty($data['modalidad'])) {
            $errors[] = 'La modalidad es obligatoria.';
        }

        return $errors;
    }

    private function uploadFile($file)
    {
        $targetDir = "./uploads/";
        $filename = basename($file['name']);
        $targetFilePath = $targetDir . $filename;


        if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            return $filename;
        } else {
            return false;
        }
    }

    public function showEditFormTutoria()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $rolesPermitidos = [1, 4];
        if (!isset($_SESSION['user']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        $csrf_token = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $csrf_token;

        if (!isset($_POST['idTutoria']) && !isset($_GET['idTutoria'])) {
            $_SESSION['message'] = 'No se especificó la tutoría a editar.';
            header('Location: ' . BASE_URL . '/tutorias.php');
            exit();
        }

        $idTutoria = $_POST['idTutoria'] ?? $_GET['idTutoria'];

        $correoInstitucional = $_SESSION['correoInstitucional'];
        $idTutor = $this->tutorModel->getIdTutorByCorreo($correoInstitucional);

        if (!$idTutor) {
            $_SESSION['message'] = 'No se encontró el tutor.';
            header('Location: ' . BASE_URL . '/menu.php');
            exit();
        }

        $tutoria = $this->tutoriaModel->getTutoriaById($idTutoria);
        if (!$tutoria) {
            $_SESSION['message'] = 'No se encontró la tutoría a editar.';
            header('Location: ' . BASE_URL . '/tutorias.php');
            exit();
        }

        $carreras = $this->tutoriaModel->getCarrerasByTutor($idTutor);
        $periodos = $this->periodoModel->getCurrentPeriodo();

        $lugar = htmlspecialchars($tutoria['lugar'] ?? '', ENT_QUOTES, 'UTF-8');
        $fechaInicio = htmlspecialchars($tutoria['fechaInicio'] ?? '', ENT_QUOTES, 'UTF-8');
        $fechaFin = htmlspecialchars($tutoria['fechaFin'] ?? '', ENT_QUOTES, 'UTF-8');
        $horaInicio = htmlspecialchars($tutoria['horaInicio'] ?? '', ENT_QUOTES, 'UTF-8');
        $horaFin = htmlspecialchars($tutoria['horaFin'] ?? '', ENT_QUOTES, 'UTF-8');
        $notas = htmlspecialchars($tutoria['nota'] ?? '', ENT_QUOTES, 'UTF-8');

        $menu = BASE_URL . '/menu.php';

        require '../views/editarTutoria.php';
    }

    public function updateTutoria()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $rolesPermitidos = [1, 4];

        if (!isset($_SESSION['user']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['idTutoria'])) {
            $_SESSION['message'] = 'Solicitud no válida.';
            header('Location: ' . BASE_URL . '/tutorias.php');
            exit();
        }

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['message'] = 'Token CSRF inválido';
            header('Location: ' . BASE_URL . '/editarTutoria.php');
            exit();
        }

        $idTutoria = $_POST['idTutoria'];
        $correoInstitucional = $_SESSION['correoInstitucional'];

        $idTutor = $this->tutorModel->getIdTutorByCorreo($correoInstitucional);

        if (!$idTutor) {
            $_SESSION['message'] = 'No se encontró el tutor.';
            header('Location: ' . BASE_URL . 'menu.php');
            exit();
        }

        $creadorCorreo = $this->tutoriaModel->getCorreoCreadorTutoria($idTutoria);

        if ($creadorCorreo !== $correoInstitucional) {
            $_SESSION['message'] = 'No tienes permiso para modificar esta tutoría.';
            header('Location: ' . BASE_URL . '/tutorias.php');
            exit();
        }

        $errors = $this->validateTutoriaData($_POST);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: ' . BASE_URL . '/editarTutoria.php?idTutoria=' . $idTutoria);
            exit();
        }

        $archivoNombre = $this->tutoriaModel->handleUploadedFile($_FILES['archivo_horario'], $idTutoria);

        $actualizado = $this->tutoriaModel->updateTutoria($idTutoria, $idTutor, $_POST, $archivoNombre);

        header('Location: ' . BASE_URL . '/tutorias.php');
        exit();
    }

    public function eliminarTutoria()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $rolesPermitidos = [1, 4];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/tutorias.php');
            exit();
        }

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            echo json_encode(['status' => 'error', 'message' => 'Token CSRF inválido.']);
            exit();
        }

        $idTutoria = $_POST['idTutoria'];
        $correoInstitucional = $_SESSION['correoInstitucional'];

        $idTutor = $this->tutorModel->getIdTutorByCorreo($correoInstitucional);

        if (!$idTutor) {
            echo json_encode(['status' => 'error', 'message' => 'No se encontró el tutor.']);
            exit();
        }

        $eliminado = $this->tutoriaModel->eliminarTutoria($idTutoria, $idTutor);

        if ($eliminado) {
            echo json_encode(['status' => 'success', 'message' => 'Tutoría eliminada con éxito.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No tienes permiso para eliminar esta tutoría o no existe.']);
        }
        exit();
    }

    public function showTutoria()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $rolesPermitidos = [1, 2, 4];
        if (!isset($_SESSION['user']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        $idTutoria = $_POST['idTutoria'] ?? $_GET['idTutoria'];

        if (!$idTutoria) {
            $_SESSION['message'] = 'No se especificó la tutoría.';
            header('Location: ' . BASE_URL . '/tutorias.php');
            exit();
        }

        $tutoria = $this->tutoriaModel->getTutoriaById($idTutoria);

        if (!$tutoria) {
            $_SESSION['message'] = 'No se encontró la tutoría.';
            header('Location: ' . BASE_URL . '/tutorias.php');
            exit();
        }

        $carrera = $this->carreraModel->getCarreraById($tutoria['carrera']);
        $numTutoria = htmlspecialchars($tutoria['numTutoria'] ?? '', ENT_QUOTES, 'UTF-8');
        $periodo = $this->periodoModel->getPeriodoById($tutoria['periodo']);
        $modalidad = htmlspecialchars($tutoria['modalidad'] ?? '', ENT_QUOTES, 'UTF-8');
        $lugar = htmlspecialchars($tutoria['lugar'] ?? '', ENT_QUOTES, 'UTF-8');
        $fechaInicio = htmlspecialchars($tutoria['fechaInicio'] ?? '', ENT_QUOTES, 'UTF-8');
        $fechaFin = htmlspecialchars($tutoria['fechaFin'] ?? '', ENT_QUOTES, 'UTF-8');
        $horaInicio = htmlspecialchars($tutoria['horaInicio'] ?? '', ENT_QUOTES, 'UTF-8');
        $horaFin = htmlspecialchars($tutoria['horaFin'] ?? '', ENT_QUOTES, 'UTF-8');
        $notas = htmlspecialchars($tutoria['nota'] ?? '', ENT_QUOTES, 'UTF-8');
        
        $menu = BASE_URL . '/menu.php';

        require '../views/verTutoria.php';
    }
}
?>