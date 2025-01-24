<?php
require_once '../config/config.php';
require_once '../config/connection.php';
require_once '../models/Profesor.php';
require_once '../models/Estudiante.php';
require_once '../models/Carrera.php';
require_once '../models/PeriodoEscolar.php';
require_once '../models/ExperienciaEducativa.php';

require_once './libs/phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportacionController
{
    private $conn;
    private $tutorModel;
    private $tutoradoModel;
    private $carreraModel;
    private $periodoModel;
    private $experienciaModel;

    public function __construct()
    {
        $this->conn = connectiondb();
        $this->tutorModel = new Profesor($this->conn);
        $this->tutoradoModel = new Estudiante($this->conn);
        $this->carreraModel = new Carrera($this->conn);
        $this->periodoModel = new PeriodoEscolar($this->conn);
        $this->experienciaModel = new ExperienciaEducativa($this->conn);
    }

    public function showImportForm()
    {
        session_start();

        $rolesPermitidos = [3];
        if (!isset($_SESSION['user']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        $user = $_SESSION['user'];
        $csrf_token = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $csrf_token;

        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
            unset($_SESSION['message']);
        } else {
            $message = null;
        }

        require_once '../views/importarDatos.php';
    }

    public function importarDatos()
    {
        session_start();

        $rolesPermitidos = [3];

        if (!isset($_SESSION['user']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $_SESSION['message'] = 'Token CSRF inválido.';
                header('Location: ' . BASE_URL . '/importarDatos.php');
                exit();
            }

            $mensajeImportacion = '';

            if (isset($_FILES['archivo_tutor']) && $_FILES['archivo_tutor']['error'] == 0) {
                $resultadoTutor = $this->importarTutores($_FILES['archivo_tutor']['tmp_name']);
                $mensajeImportacion .= $resultadoTutor ? "Importación de tutores completada con éxito.<br>" : "Error en la importación de tutores.<br>";
            }

            if (isset($_FILES['archivo_tutorado']) && $_FILES['archivo_tutorado']['error'] == 0) {
                $resultadoTutorado = $this->importarTutorados($_FILES['archivo_tutorado']['tmp_name']);
                $mensajeImportacion .= $resultadoTutorado ? "Importación de tutorados completada con éxito.<br>" : "Error en la importación de tutorados.<br>";
            }

            if (isset($_FILES['archivo_carrera']) && $_FILES['archivo_carrera']['error'] == 0) {
                $resultadoCarrera = $this->importarCarreras($_FILES['archivo_carrera']['tmp_name']);
                $mensajeImportacion .= $resultadoCarrera ? "Importación de carreras completada con éxito.<br>" : "Error en la importación de carreras.<br>";
            }

            if (isset($_FILES['archivo_periodo']) && $_FILES['archivo_periodo']['error'] == 0) {
                $resultadoPeriodo = $this->importarPeriodos($_FILES['archivo_periodo']['tmp_name']);
                $mensajeImportacion .= $resultadoPeriodo ? "Importación de periodos completada con éxito.<br>" : "Error en la importación de periodos.<br>";
            }

            if (isset($_FILES['archivo_experienciaE']) && $_FILES['archivo_experienciaE']['error'] == 0) {
                $resultadoExperiencia = $this->importarExperiencias($_FILES['archivo_experienciaE']['tmp_name']);
                $mensajeImportacion .= $resultadoExperiencia ? "Importación de experiencias educativas completada con éxito.<br>" : "Error en la importación de experiencias educativas.<br>";
            }

            $_SESSION['message'] = 'Importación de datos completada con éxito.';

            header('Location: ' . BASE_URL . '/importarDatos.php');
            exit();
        } else {
            header('Location: ' . BASE_URL . '/importarDatos.php');
            exit();
        }
    }

    private function importarTutores($filePath)
    {
        try {
            $fileType = IOFactory::identify($filePath);
            $reader = IOFactory::createReader($fileType);
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            $startRow = 2;
            $highestRow = $sheet->getHighestRow();
            $filasVacias = 0;
            $maximoFilasVacias = 5;

            for ($rowIndex = $startRow; $rowIndex <= $highestRow + 1; $rowIndex++) {
                $nombre = $sheet->getCell("A" . $rowIndex)->getValue();
                $apellidoPaterno = $sheet->getCell("B" . $rowIndex)->getValue();
                $apellidoMaterno = $sheet->getCell("C" . $rowIndex)->getValue();
                $noPersonal = $sheet->getCell("D" . $rowIndex)->getValue();
                $correoInstitucional = $sheet->getCell("E" . $rowIndex)->getValue();
                $idRol = 1;

                if (!empty($nombre) || !empty($correoInstitucional)) {
                    $filasVacias = 0;

                    $data = [
                        'nombre' => $nombre,
                        'apellidoPaterno' => $apellidoPaterno,
                        'apellidoMaterno' => $apellidoMaterno,
                        'noPersonal' => $noPersonal,
                        'correoInstitucional' => $correoInstitucional,
                        'rol' => $idRol,
                    ];

                    $tutorCreado = $this->tutorModel->createProfesor($data);
                } else {
                    $filasVacias++;
                    if ($filasVacias >= $maximoFilasVacias) {
                        break;
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Error al importar tutores: " . $e->getMessage());
            return false;
        }
    }

    private function importarTutorados($filePath)
    {
        try {
            $fileType = IOFactory::identify($filePath);
            $reader = IOFactory::createReader($fileType);
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            $startRow = 2;
            $highestRow = $sheet->getHighestRow();
            $filasVacias = 0;
            $maximoFilasVacias = 5;

            for ($rowIndex = $startRow; $rowIndex <= $highestRow + 1; $rowIndex++) {
                $nombre = $sheet->getCell("A" . $rowIndex)->getValue();
                $apellidoPaterno = $sheet->getCell("B" . $rowIndex)->getValue();
                $apellidoMaterno = $sheet->getCell("C" . $rowIndex)->getValue();
                $matricula = $sheet->getCell("D" . $rowIndex)->getValue();
                $correoInstitucional = $sheet->getCell("E" . $rowIndex)->getValue();
                $idCarrera = $sheet->getCell("F" . $rowIndex)->getValue();
                $idTutor = $sheet->getCell("G" . $rowIndex)->getValue();
                $idRol = 2; 

                $idCarrera = !empty($idCarrera) ? (int) $idCarrera : null;
                $idTutor = !empty($idTutor) ? (int) $idTutor : null;

                if (!empty($nombre) || !empty($correoInstitucional) || !empty($idCarrera)) {
                    $filasVacias = 0;

                    $data = [
                        'nombre' => $nombre,
                        'apellidoPaterno' => $apellidoPaterno,
                        'apellidoMaterno' => $apellidoMaterno,
                        'matricula' => $matricula,
                        'correoInstitucional' => $correoInstitucional,
                        'carrera' => $idCarrera,
                        'tutor' => $idTutor,
                        'rol' => $idRol
                    ];

                    $tutoradoCreado = $this->tutoradoModel->createEstudiante($data);

                    if (!$tutoradoCreado) {
                        error_log("Error al crear tutorado en la fila $rowIndex");
                    }

                } else {
                    $filasVacias++;
                    if ($filasVacias >= $maximoFilasVacias) {
                        break;
                    }
                }
            }

            return true;

        } catch (Exception $e) {
            error_log("Error al importar tutorados: " . $e->getMessage());
            return false;
        }
    }

    private function importarCarreras($filePath)
    {
        try {
            $fileType = IOFactory::identify($filePath);
            $reader = IOFactory::createReader($fileType);
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            $startRow = 2;
            $highestRow = $sheet->getHighestRow();
            $filasVacias = 0;
            $maximoFilasVacias = 5;

            for ($rowIndex = $startRow; $rowIndex <= $highestRow + 1; $rowIndex++) {
                $nombre = $sheet->getCell("A" . $rowIndex)->getValue();

                if (empty($nombre)) {
                    $filasVacias++;
                    if ($filasVacias >= $maximoFilasVacias) {
                        break;
                    }
                } else {
                    $filasVacias = 0;

                    $carreraCreada = $this->carreraModel->createCarrera($nombre);
                }
            }
            return true;
        } catch (Exception $e) {
            error_log("Error al importar carreras: " . $e->getMessage());
            return false;
        }
    }

    private function importarPeriodos($filePath)
    {
        try {
            $fileType = IOFactory::identify($filePath);
            $reader = IOFactory::createReader($fileType);
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            $startRow = 2;
            $highestRow = $sheet->getHighestRow();
            $filasVacias = 0;
            $maximoFilasVacias = 5;

            for ($rowIndex = $startRow; $rowIndex <= $highestRow + 1; $rowIndex++) {
                $nombre = $sheet->getCell("A" . $rowIndex)->getValue();
                $actual = $sheet->getCell("B" . $rowIndex)->getValue();

                if (empty($nombre)) {
                    $filasVacias++;
                    if ($filasVacias >= $maximoFilasVacias) {
                        break;
                    }
                } else {
                    $filasVacias = 0;

                    $periodoCreado = $this->periodoModel->createPeriodo($nombre, $actual);
                }
            }
            return true;
        } catch (Exception $e) {
            error_log("Error al importar periodos: " . $e->getMessage());
            return false;
        }
    }

    private function importarExperiencias($filePath)
    {
        try {
            $fileType = IOFactory::identify($filePath);
            $reader = IOFactory::createReader($fileType);
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            $startRow = 2;
            $highestRow = $sheet->getHighestRow();
            $filasVacias = 0;
            $maximoFilasVacias = 5;

            for ($rowIndex = $startRow; $rowIndex <= $highestRow + 1; $rowIndex++) {
                $nombre = $sheet->getCell("A" . $rowIndex)->getValue();
                $nrc = $sheet->getCell("B" . $rowIndex)->getValue();
                $profesor = $sheet->getCell("C" . $rowIndex)->getValue();
                $programaEducativo = $sheet->getCell("D" . $rowIndex)->getValue();

                if (empty($nombre) && empty($nrc) && empty($profesor) && empty($programaEducativo)) {
                    $filasVacias++;
                    if ($filasVacias >= $maximoFilasVacias) {
                        break;
                    }
                } else {
                    $filasVacias = 0;

                    $experienciaCreada = $this->experienciaModel->createExperiencia($nombre, $nrc);
                }
            }
            return true;
        } catch (Exception $e) {
            error_log("Error al importar experiencias educativas: " . $e->getMessage());
            return false;
        }
    }
}