<?php
require_once '../config/config.php';
require_once '../config/connection.php';
require_once '../models/Tutoria.php';
require_once '../models/Coordinador.php';
require_once '../models/Reporte.php';
require_once '../models/Problematica.php';
require_once '../models/ExperienciaEducativa.php';
require_once '../models/TipoProblematica.php';
require_once '../models/Profesor.php';

class TutoriasCoordinador
{
    private $conn;
    private $tutoriaModel;
    private $coordinadorModel;
    private $reportesModel;
    private $problematicaModel;
    private $experienciaEducativaModel;
    private $tipoProblematicaModel;
    private $profesorModel;

    public function __construct()
    {
        $this->conn = connectiondb();
        $this->tutoriaModel = new Tutoria($this->conn);
        $this->coordinadorModel = new Coordinador($this->conn);
        $this->reportesModel = new Reporte($this->conn);
        $this->problematicaModel = new Problematica($this->conn);
        $this->experienciaEducativaModel = new ExperienciaEducativa($this->conn);
        $this->tipoProblematicaModel = new TipoProblematica($this->conn);
        $this->profesorModel = new Profesor($this->conn);
    }

    public function showTutorias()
    {
        session_start();

        $rolesPermitidos = [4]; // Rol para coordinador
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        $user = $_SESSION['user'];
        $correoCoordinador = $_SESSION['correoInstitucional'];

        $csrf_token = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $csrf_token;

        // Obtener idSesion del coordinador utilizando el modelo
        $idSesion = $this->coordinadorModel->getIdSesionByCorreo($correoCoordinador);

        if ($idSesion) {
            $_SESSION['idSesion'] = $idSesion;

            // Obtener las tutorías para las carreras asociadas al coordinador
            $tutorias = $this->tutoriaModel->getTutoriasByCoordinador($idSesion);
        } else {
            // Manejar el caso en que no se encuentre el idSesion (esto no debería ocurrir)
            $tutorias = [];
        }

        // Renderizar la vista
        require_once '../views/consultarTutorias.php';
    }

    public function showReportesTutorias()
    {
        session_start();

        $rolesPermitidos = [4];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        $user = $_SESSION['user'];
        $correoCoordinador = $_SESSION['correoInstitucional'];

        $csrf_token = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $csrf_token;

        $idSesion = $this->coordinadorModel->getIdSesionByCorreo($correoCoordinador);

        if ($idSesion) {
            $_SESSION['idSesion'] = $idSesion;

            $reportes = $this->reportesModel->getReportesByCoordinador($idSesion);
        } else {
            $reportes = [];
        }

        $muestraActual = true;
        require_once '../views/consultarReportes.php';
    }

    public function showHistorialReportesTutorias()
    {
        session_start();

        $rolesPermitidos = [4];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        $user = $_SESSION['user'];
        $correoCoordinador = $_SESSION['correoInstitucional'];

        $csrf_token = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $csrf_token;

        $idSesion = $this->coordinadorModel->getIdSesionByCorreo($correoCoordinador);

        if ($idSesion) {
            $_SESSION['idSesion'] = $idSesion;

            $reportes = $this->reportesModel->getHistorialReportesByCoordinador($idSesion);
        } else {
            $reportes = [];
        }

        $muestraActual = false;
        require_once '../views/consultarReportes.php';
    }

    public function generatePDFReportSummary() 
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $allowedRole = [4];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $allowedRole)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        $numTutoringSession = $_POST['numTutoria'] ?? null;
        $reportType = $_POST['tipoReporte'] ?? null;
        $careers = $_POST['carreras'] ?? null;

        if (!$numTutoringSession || !$reportType || !$careers) {
            $_SESSION['error'] = 'Ocurrió un error al generar el concentrado de reportes';
            header('Location: ' . BASE_URL . '/consultarReportes.php');
            exit();
        }

        $idSession = $_SESSION['idSesion']; 

        $reports = $this->reportesModel->getReportSummaryByCoordinator($idSession, $numTutoringSession, $careers);

        if ($reportType === 'problematicas') {
            $this->generateProblemSummary($reports);
        } elseif ($reportType === 'comentarios') {
            $this->generateCommentsSummary($reports);
        }
    }

    private function generateProblemSummary($reports) 
    {
        $dataProblems = [];

        foreach ($reports as $report) {
            $idReport = $report['idReporte'];
            $career = $report['carrera']; 

            $problems = $this->problematicaModel->getProblematicasByReporte($idReport);

            foreach($problems as $problem) {
                $experienciaEducativa = $this->experienciaEducativaModel->getExperienciaById($problem['experienciaEducativa']);
                $professor = $this->profesorModel->getProfesorById($problem['profesor']);
                $problematic = $this->problematicaModel->getProblematicaById($problem['problematica']);
                $problemType = $this->tipoProblematicaModel->getTiposProblematicasById($problematic['tipoProblematica']);

                $dataProblems[$career][] = [
                    'experienciaEducativa' => $experienciaEducativa['nombre'],
                    'profesor' => $professor['nombre'] . ' ' . $professor['apellidoPaterno'] . ' ' . $professor['apellidoMaterno'],
                    'problematica' => $problematic['descripcion'],
                    'tipoProblematica' => $problemType['nombre'],
                    'numAlumnos' => $problem['numAlumnos'],
                ];
            }
        }

        $coordinator = $this->coordinadorModel->getCoordinadorById($_SESSION['idSesion']);

        $numTutoringSession = $_POST['numTutoria'];
        $careers = $_POST['carreras'];

        require_once '../views/generarReporteConcentradoPDF.php';
    }

    private function generateCommentsSummary($reportes) 
    {
        
    }
}
?>