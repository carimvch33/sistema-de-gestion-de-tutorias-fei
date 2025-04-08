<?php
require_once '../config/config.php';
require_once '../config/connection.php';
require_once '../models/Reporte.php';
require_once '../models/Problematica.php';
require_once '../models/Carrera.php';
require_once '../models/Profesor.php';
require_once '../models/PeriodoEscolar.php';
require_once '../models/Tutoria.php';
require_once '../models/ExperienciaEducativa.php';

class ReporteController
{
    private $conn;
    private $reporteModel;
    private $problematicaModel;
    private $periodoModel;
    private $carreraModel;
    private $profesorModel;
    private $tutoriaModel;
    private $experienciaEducativaModel;

    public function __construct()
    {
        $this->conn = connectiondb();
        $this->reporteModel = new Reporte($this->conn);
        $this->problematicaModel = new Problematica($this->conn);
        $this->periodoModel = new PeriodoEscolar($this->conn);
        $this->carreraModel = new Carrera($this->conn);
        $this->profesorModel = new Profesor($this->conn);
        $this->tutoriaModel = new Tutoria($this->conn);
        $this->experienciaEducativaModel = new ExperienciaEducativa($this->conn);
    }

    public function showReportes()
    {
        session_start();

        define('logo_UV', BASE_URL . '/img/UV.png');

        $rolesPermitidos = [1, 4];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        if (isset($_SESSION['errors'])) {
            $errors = $_SESSION['errors'];
            unset($_SESSION['errors']);
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $user = $_SESSION['user'];

        $userCorreo = $_SESSION['correoInstitucional'];

        $reportes = $this->reporteModel->getReportesByTutor($userCorreo);
        $tutorId = $this->reporteModel->getTutorIdByCorreo($userCorreo);
        $carrerasTutor = $this->tutoriaModel->getCarrerasByTutor($tutorId);

        $carreras = [];
        while ($row = $carrerasTutor->fetch_assoc()) {
            if (preg_match('/\((.*?)\)/', $row['nombre'], $matches)) {
                $carreras[] = $matches[1]; 
            }
        }
        

        $muestraActual = true;
        $menu = BASE_URL . '/cerrarSesion.php';

        switch ($_SESSION['rol']) {
            case 1:
                $menu = BASE_URL . '/menu.php';
                break;
            case 4:
                $menu = BASE_URL . '/menu.php';
                break;
            default:
                $menu = BASE_URL . '/cerrarSesion.php';
                break;
        }

        require_once '../views/administrarReportes.php';
    }

    public function showReportHistory() 
    {
        session_start();

        $rolesPermitidos = [1, 4];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        if (isset($_SESSION['errors'])) {
            $errors = $_SESSION['errors'];
            unset($_SESSION['errors']);
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $user = $_SESSION['user'];

        $userCorreo = $_SESSION['correoInstitucional'];

        $reportes = $this->reporteModel->getReportHistoryByTutor($userCorreo);
        $tutorId = $this->reporteModel->getTutorIdByCorreo($userCorreo);
        $carrerasTutor = $this->tutoriaModel->getCarrerasByTutor($tutorId);

        $carreras = [];
        while ($row = $carrerasTutor->fetch_assoc()) {
            if (preg_match('/\((.*?)\)/', $row['nombre'], $matches)) {
                $carreras[] = $matches[1]; 
            }
        }

        $muestraActual = false;
        $menu = BASE_URL . '/cerrarSesion.php';

        switch ($_SESSION['rol']) {
            case 1:
                $menu = BASE_URL . '/menu.php';
                break;
            case 4:
                $menu = BASE_URL . '/menu.php';
                break;
            default:
                $menu = BASE_URL . '/cerrarSesion.php';
                break;
        }

        require_once '../views/administrarReportes.php';
    }

    public function showRegistroForm()
    {
        session_start();

        $rolesPermitidos = [1, 4];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $csrf_token = $_SESSION['csrf_token'];
        $user = $_SESSION['user'];
        $perfilActual = $_SESSION['correoInstitucional'];
        $periodoActual = $_SESSION['periodoActual'];


        $tutor = $this->reporteModel->getTutorIdByCorreo($perfilActual);
        $carreras = $this->tutoriaModel->getCarrerasByTutor($tutor);


        $periodoData = $this->periodoModel->getPeriodoByNombre($periodoActual);
        $periodo = $periodoData['idPeriodo'];

        $problematicas = $this->problematicaModel->getProblematicas();

        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);

        $menu = BASE_URL . '/cerrarSesion.php';
        switch ($_SESSION['rol']) {
            case 1:
                $menu = BASE_URL . '/menu.php';
                break;
            case 4:
                $menu = BASE_URL . '/menu.php';
                break;
        }

        require_once '../views/registroReporteTutoria.php';
    }

    public function createReporte()
    {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            echo "Error: Solicitud no válida.";
            exit();
        }

        $rolesPermitidos = [1, 4];
        if (!isset($_SESSION['user']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        $errors = [];

        $idTutor = $_POST['tutor'] ?? null;
        $carrera = $_POST['carrera'] ?? null;
        $numTutoria = $_POST['numTutoria'] ?? null;
        $fechaInicio = $_POST['fechaInicio'] ?? null;
        $fechaFin = $_POST['fechaFin'] ?? null;
        $numAsistencias = $_POST['numAsistencias'] ?? null;
        $numRiesgo = $_POST['numRiesgo'] ?? null;
        $comentario = $_POST['comentario'] ?? null;
        $accion = $_POST['accion'] ?? 'enviar';
        $fechaActual = date('Y-m-d');

        if ($accion === 'enviar') {
            if (!$carrera)
                $errors[] = 'El campo "Carrera" es obligatorio.';
            if (!$numTutoria)
                $errors[] = 'El campo "Número de tutoría" es obligatorio.';
            if (!$fechaInicio)
                $errors[] = 'El campo "Fecha de inicio" es obligatorio.';
            if (!$fechaFin)
                $errors[] = 'El campo "Fecha de fin" es obligatorio.';
            if ($numAsistencias === '' || $numAsistencias === null)
                $errors[] = 'El campo "Número de asistencias" es obligatorio.';
            if ($numRiesgo === '' || $numRiesgo === null)
                $errors[] = 'El campo "Número de tutorados en riesgo" es obligatorio.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: ' . BASE_URL . '/registroReporte.php');
            exit();
        }

        $periodoActual = $_SESSION['periodoActual'];
        $periodoData = $this->periodoModel->getPeriodoByNombre($periodoActual);
        $periodo = $periodoData['idPeriodo'];

        $this->conn->begin_transaction();
        try {
            $idCarreraTutor = $this->reporteModel->createCarreraTutor($carrera, $idTutor);

            $esBorrador = ($accion === 'borrador') ? 0 : 1;

            $reporteData = [
                'carreraTutor' => $idCarreraTutor,
                'periodo' => $periodo,
                'numTutoria' => $numTutoria,
                'fechaInicio' => $fechaInicio,
                'fechaFin' => $fechaFin,
                'numAsistencias' => $numAsistencias,
                'numRiesgo' => $numRiesgo,
                'comentario' => $comentario,
                'fechaCreacion' => $fechaActual,
                'esBorrador' => $esBorrador
            ];
            $idReporte = $this->reporteModel->createReporteTutoria($reporteData);

            if ($accion === 'enviar' && $_POST['tipo'] === 'problematica') {
                $this->handleProblematicas($idReporte);
            }

            $this->conn->commit();
            header("Location: " . BASE_URL . "/administrarReportes.php");
            exit();
        } catch (Exception $e) {
            $this->conn->rollback();
            $_SESSION['errors'] = $errors ?: ['Error al registrar el reporte de tutoría.'];
            header('Location: ' . BASE_URL . '/registroReporte.php');
            exit();
        }
    }

    private function handleProblematicas($idReporte)
    {
        $experiencias = $_POST['experienciaE'] ?? [];
        $profesores = $_POST['profesor'] ?? [];
        $problematicas = $_POST['problematica'] ?? [];
        $otros = $_POST['otro'] ?? [];
        $numAfectados = $_POST['numAlumnos'] ?? [];
        $estado = 'En revisión';

        $errors = [];
        for ($i = 0; $i < count($experiencias); $i++) {
            if (empty($experiencias[$i]))
                $errors[] = "La experiencia educativa en la línea " . ($i + 1) . " es obligatoria.";
            if (empty($profesores[$i]))
                $errors[] = "El profesor en la línea " . ($i + 1) . " es obligatorio.";
            if (empty($problematicas[$i]) && empty($otros[$i]))
                $errors[] = "La problemática en la línea " . ($i + 1) . " es obligatoria.";
            if (empty($numAfectados[$i]))
                $errors[] = "El número de alumnos en la línea " . ($i + 1) . " es obligatorio.";
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            throw new Exception('Datos incompletos en problemática.');
        }

        $problematicaData = [];
        for ($i = 0; $i < count($experiencias); $i++) {
            $problematicaId = $problematicas[$i] === 'otro' ? null : $problematicas[$i];
            $otro = $problematicas[$i] === 'otro' ? $otros[$i] : null;

            $problematicaData[] = [
                'experiencia' => $experiencias[$i],
                'profesor' => $profesores[$i],
                'problematica' => $problematicaId,
                'otro' => $otro,
                'numAlumnos' => $numAfectados[$i],
                'estado' => $estado,
                'reporte' => $idReporte
            ];
        }

        $this->reporteModel->insertProblematicasAcademicas($problematicaData);
    }

    public function getCarreraDatos()
    {
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Solicitud inválida']);
            exit();
        }

        if (!isset($_POST['idCarrera'])) {
            echo json_encode(['error' => 'ID de carrera no proporcionado']);
            exit();
        }

        $idCarrera = $_POST['idCarrera'];

        $experiencias = $this->experienciaEducativaModel->getExperienciasByCarrera($idCarrera);

        $profesores = $this->profesorModel->getProfesores();

        $problematicas = $this->problematicaModel->getProblematicas();

        require_once '../models/Seccion.php';
        $seccionModel = new Seccion($this->conn);
        $secciones = $seccionModel->getSeccionesByCarrera($idCarrera);

        header('Content-Type: application/json');
        echo json_encode([
            'experiencias' => $experiencias,
            'profesores' => $profesores,
            'problematicas' => $problematicas,
            'secciones' => $secciones
        ]);
    }

    public function showEditForm()
    {
        session_start();

        $rolesPermitidos = [1, 4];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $csrf_token = $_SESSION['csrf_token'];
        $user = $_SESSION['user'];
        $perfilActual = $_SESSION['correoInstitucional'];

        if (isset($_GET['idReporte'])) {
            $idReporte = $_GET['idReporte'];
        } elseif (isset($_POST['idReporte'])) {
            $idReporte = $_POST['idReporte'];
        } else {
            echo "Error: No se recibió el ID del reporte.";
            exit();
        }

        $tutor = $this->reporteModel->getTutorIdByCorreo($perfilActual);

        $reporte = $this->reporteModel->getReporteById($idReporte);

        if (!$reporte) {
            $_SESSION['errors'] = ['No se encontró el reporte'];
            header("Location: " . BASE_URL . "/administrarReportes.php");
            exit();
        }

        $carreras = $this->tutoriaModel->getCarrerasByTutor($tutor);
        $periodos = $this->periodoModel->getCurrentPeriodo();

        $problematicasReporte = $this->problematicaModel->getProblematicasByReporte($idReporte);

        $carreraId = $reporte['carrera'];

        $experiencias = $this->experienciaEducativaModel->getExperienciasByCarrera($carreraId);
        $profesores = $this->profesorModel->getProfesores();
        $listaProblematicas = $this->problematicaModel->getProblematicas();

        require_once '../models/Seccion.php';
        $seccionModel = new Seccion($this->conn);
        $secciones = $seccionModel->getSeccionesByCarrera($reporte['carrera']);


        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);

        $menu = BASE_URL . '/cerrarSesion.php';
        switch ($_SESSION['rol']) {
            case 1:
                $menu = BASE_URL . '/menu.php';
                break;
            case 4:
                $menu = BASE_URL . '/menu.php';
                break;
        }

        require_once '../views/editarReporteTutoria.php';
    }

    public function updateReporte()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            echo "Error: Solicitud no válida.";
            exit();
        }

        $rolesPermitidos = [1, 4];
        if (!isset($_SESSION['user']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        $errors = [];

        $idReporte = $_POST['idReporte'] ?? null;
        $carrera = $_POST['carrera'] ?? null;
        $periodo = $_POST['periodo'] ?? null;
        $numTutoria = $_POST['numTutoria'] ?? null;
        $fechaInicio = $_POST['fechaInicio'] ?? null;
        $fechaFin = $_POST['fechaFin'] ?? null;
        $numAsistencias = $_POST['numAsistencias'] ?? null;
        $numRiesgo = $_POST['numRiesgo'] ?? null;
        $comentario = $_POST['comentario'] ?? null;

        if (!$idReporte)
            $errors[] = 'ID del reporte no proporcionado.';
        if (!$carrera)
            $errors[] = 'El campo "Carrera" es obligatorio.';
        if (!$periodo)
            $errors[] = 'El campo "Periodo" es obligatorio.';
        if (!$numTutoria)
            $errors[] = 'El campo "Número de tutoría" es obligatorio.';
        if (!$fechaInicio)
            $errors[] = 'El campo "Fecha de inicio" es obligatorio.';
        if (!$fechaFin)
            $errors[] = 'El campo "Fecha de fin" es obligatorio.';
        if ($numAsistencias === '' || $numAsistencias === null)
            $errors[] = 'El campo "Número de asistencias" es obligatorio.';
        if ($numRiesgo === '' || $numRiesgo === null)
            $errors[] = 'El campo "Número de tutorados en riesgo" es obligatorio.';

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: " . BASE_URL . "/editarReporte.php?idReporte=$idReporte");
            exit();
        }

        try {
            $this->conn->begin_transaction();

            $reporteData = [
                'carrera' => $carrera,
                'periodo' => $periodo,
                'numTutoria' => $numTutoria,
                'fechaInicioTutoria' => $fechaInicio,
                'fechaFinTutoria' => $fechaFin,
                'numAsistencia' => $numAsistencias,
                'numRiesgo' => $numRiesgo,
                'comentario' => $comentario,
            ];
            $this->reporteModel->updateReporteTutoria($idReporte, $reporteData);

            if ($_POST['tipo'] === 'problematica') {
                $this->updateProblematicas($idReporte);
            }

            $this->conn->commit();
            header("Location: " . BASE_URL . "/administrarReportes.php");
            exit();
        } catch (Exception $e) {
            $this->conn->rollback();
            $_SESSION['errors'] = ['Error al actualizar el reporte de tutoría. Detalles: ' . $e->getMessage()];
            header("Location: " . BASE_URL . "/editarReporte.php?idReporte=$idReporte");
            exit();
        }
    }

    private function updateProblematicas($idReporte)
    {
        $experiencias = $_POST['experienciaE'] ?? [];
        $profesores = $_POST['profesor'] ?? [];
        $problematicas = $_POST['problematica'] ?? [];
        $otros = $_POST['otro'] ?? [];
        $numAfectados = $_POST['numAlumnos'] ?? [];

        $errors = [];
        for ($i = 0; $i < count($experiencias); $i++) {
            if (empty($experiencias[$i]))
                $errors[] = "La experiencia educativa en la línea " . ($i + 1) . " es obligatoria.";
            if (empty($profesores[$i]))
                $errors[] = "El profesor en la línea " . ($i + 1) . " es obligatorio.";
            if (empty($problematicas[$i]) && empty($otros[$i]))
                $errors[] = "La problemática en la línea " . ($i + 1) . " es obligatoria.";
            if (empty($numAfectados[$i]))
                $errors[] = "El número de alumnos en la línea " . ($i + 1) . " es obligatorio.";
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            throw new Exception('Datos incompletos en problemática.');
        }

        $this->problematicaModel->deleteProblematicasByReporte($idReporte);

        $problematicaData = [];
        $estado = 'En revisión';
        for ($i = 0; $i < count($experiencias); $i++) {
            $problematicaId = $problematicas[$i] === 'otro' ? null : $problematicas[$i];
            $otro = $problematicas[$i] === 'otro' ? $otros[$i] : null;

            $problematicaData[] = [
                'experiencia' => $experiencias[$i],
                'profesor' => $profesores[$i],
                'problematica' => $problematicaId,
                'otro' => $otro,
                'numAlumnos' => $numAfectados[$i],
                'estado' => $estado,
                'reporte' => $idReporte
            ];
        }

        $this->reporteModel->insertProblematicasAcademicas($problematicaData);
    }

    public function deleteReporte()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            echo json_encode(['status' => 'error', 'message' => 'Token CSRF inválido.']);
            exit();
        }

        $rolesPermitidos = [1, 4];
        if (!isset($_SESSION['user']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
            echo json_encode(['status' => 'error', 'message' => 'No tienes permiso para realizar esta acción.']);
            exit();
        }

        $idReporte = isset($_POST['idReporte']) ? $_POST['idReporte'] : '';

        if (!$idReporte) {
            echo json_encode(['status' => 'error', 'message' => 'ID del reporte no proporcionado.']);
            exit();
        }

        $correoActual = $_SESSION['correoInstitucional'];

        $tutorData = $this->reporteModel->getTutorByReporteId($idReporte);

        if (!$tutorData || $tutorData['correoInstitucional'] !== $correoActual) {
            echo json_encode(['status' => 'error', 'message' => 'No tienes permiso para eliminar este reporte.']);
            exit();
        }

        $this->conn->begin_transaction();
        try {
            $this->problematicaModel->deleteProblematicasByReporte($idReporte);

            $reporte = $this->reporteModel->getReporteById($idReporte);
            $idCarreraTutor = $reporte['carreraTutor'];

            $this->reporteModel->deleteReporteTutoria($idReporte);

            $this->reporteModel->deleteCarreraTutorIfUnused($idCarreraTutor);

            $this->conn->commit();

            echo json_encode(['status' => 'success', 'message' => 'Reporte de tutoría eliminado con éxito.']);
            exit();
        } catch (Exception $e) {
            $this->conn->rollback();
            echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el reporte de tutoría.']);
            exit();
        }
    }

    public function getProfesoresPorExperiencia()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {

            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                echo json_encode(['error' => 'Token CSRF inválido.']);
                exit();
            }

            $idExperiencia = $_POST['idExperiencia'] ?? null;
            $idCarrera = $_POST['idCarrera'] ?? null;

            if ($idExperiencia && $idCarrera) {
                require_once '../models/Seccion.php';
                $seccionModel = new Seccion($this->conn);

                $profesores = $seccionModel->getProfesoresPorExperiencia($idExperiencia, $idCarrera);

                echo json_encode(['profesores' => $profesores]);
            } else {
                echo json_encode(['error' => 'Datos incompletos.']);
            }
        } else {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Solicitud inválida.']);
        }
    }

    public function getExperienciasPorProfesor()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {

            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                echo json_encode(['error' => 'Token CSRF inválido.']);
                exit();
            }

            $idProfesor = $_POST['idProfesor'] ?? null;
            $idCarrera = $_POST['idCarrera'] ?? null;

            if ($idProfesor && $idCarrera) {
                require_once '../models/Seccion.php';
                $seccionModel = new Seccion($this->conn);

                $experiencias = $seccionModel->getExperienciasPorProfesor($idProfesor, $idCarrera);

                echo json_encode(['experiencias' => $experiencias]);
            } else {
                echo json_encode(['error' => 'Datos incompletos.']);
            }
        } else {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Solicitud inválida.']);
        }
    }

    public function showReporte() 
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $rolesPermitidos = [1, 4];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        $idTutoria = $_POST['idTutoria'] ?? $_GET['idTutoria'];

        if (!$idTutoria) {
            $_SESSION['message'] = 'No se especificó el reporte de tutoría.';
            header('Location: ' . BASE_URL . '/administrarReportes.php');
            exit();
        }

        $reporte = $this->reporteModel->getReporteById($idTutoria);

        if (!$reporte) {
            $_SESSION['message'] = 'No se encontró el reporte de tutoría.';
            header('Location: ' . BASE_URL . '/administrarReportes.php');
            exit();
        }

        $problematicasReporte = $this->problematicaModel->getProblematicasByReporte($idTutoria);

        $carreraId = $reporte['carrera'];

        $experiencias = $this->experienciaEducativaModel->getExperienciasByCarrera($carreraId);
        $profesores = $this->profesorModel->getProfesores();
        $listaProblematicas = $this->problematicaModel->getProblematicas();

        $menu = BASE_URL . '/cerrarSesion.php';
        switch ($_SESSION['rol']) {
            case 1:
                $menu = BASE_URL . '/menu.php';
                break;
            case 4:
                $menu = BASE_URL . '/menu.php';
                break;
        }

        require_once '../views/verReporteTutoria.php';
    }

    public function generateTutoringReport() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $rolesPermitidos = [1, 4];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        $idTutoria = $_POST['idTutoria'] ?? $_GET['idTutoria'];

        if (!$idTutoria) {
            $_SESSION['message'] = 'No se especificó el reporte de tutoría.';
            header('Location: ' . BASE_URL . '/administrarReportes.php');
            exit();
        }

        $reporte = $this->reporteModel->getReporteById($idTutoria);

        if (!$reporte) {
            $_SESSION['message'] = 'No se encontró el reporte de tutoría.';
            header('Location: ' . BASE_URL . '/administrarReportes.php');
            exit();
        }

        $problematicasReporte = $this->problematicaModel->getProblematicasByReporte($idTutoria);
        $carreraId = $reporte['carrera'];

        $experiencias = $this->experienciaEducativaModel->getExperienciasByCarrera($carreraId);
        $profesores = $this->profesorModel->getProfesores();
        $listaProblematicas = $this->problematicaModel->getProblematicas();

        $menu = BASE_URL . '/cerrarSesion.php';
        switch ($_SESSION['rol']) {
            case 1:
                $menu = BASE_URL . '/menu.php';
                break;
            case 4:
                $menu = BASE_URL . '/menu.php';
                break;
        }
        require_once '../views/generarReporteTutoria.php';
    }
}
?>