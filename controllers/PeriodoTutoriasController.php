<?php
require_once '../config/config.php';
require_once '../config/connection.php';
require_once '../models/PeriodoTutorias.php';

class PeriodoTutoriasController
{
    private $conn;
    private $periodoTutoriasModel;

    public function __construct()
    {
        $this->conn = connectiondb();
        $this->periodoTutoriasModel = new PeriodoTutorias($this->conn);
    }

    public function getPeriodoTutoriasByCarrera()
    {
        header('Content-Type: application/json');
        session_start();

        $rolesPermitidos = [1];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            exit();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $idCarrera = isset($_GET['idCarrera']) ? intval($_GET['idCarrera']) : 0;

        try {
            $periodoTutorias = $this->periodoTutoriasModel->getPeriodosByCarrera($idCarrera);
            
            foreach ($periodoTutorias as &$tutoria) {
                $fechaInicio = $tutoria['fechaInicio'];
                $fechaFin = $tutoria['fechaFin'];
        
                $tutoria['fechaInicioFormateada'] = date('d/m/Y', strtotime($fechaInicio));
                $tutoria['fechaFinFormateada'] = date('d/m/Y', strtotime($fechaFin));
                $tutoria['mismaFecha'] = ($fechaInicio === $fechaFin);
            }

            echo json_encode($periodoTutorias ?? []);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
?>