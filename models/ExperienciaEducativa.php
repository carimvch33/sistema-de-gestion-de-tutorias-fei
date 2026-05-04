<?php
class ExperienciaEducativa
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getExperiencias()
    {
        $stmt = $this->conn->prepare("
            SELECT ee.idExperienciaEducativa, 
                   ee.nombre AS nombreEE, 
                   c.nombre AS nombrePrograma 
            FROM experiencia_educativa ee 
            INNER JOIN carrera c ON c.idCarrera = ee.programaEducativo
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $experiencias = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $experiencias;
    }

    public function getExperienciaById($idExperiencia)
    {
        $stmt = $this->conn->prepare("
            SELECT ee.idExperienciaEducativa, ee.nombre, ee.programaEducativo
            FROM experiencia_educativa ee
            WHERE ee.idExperienciaEducativa = ?
        ");
        $stmt->bind_param("i", $idExperiencia);
        $stmt->execute();
        $result = $stmt->get_result();
        $experiencia = $result->fetch_assoc();
        $stmt->close();
        return $experiencia;
    }


    public function createExperiencia($nombre, $programa)
    {
        $stmt = $this->conn->prepare("INSERT INTO experiencia_educativa (nombre, programaEducativo) VALUES (?, ?)");
        $stmt->bind_param("si", $nombre, $programa);

        try{
            $result = $stmt->execute();
            $stmt->close();
            return $result;

        } catch (mysqli_sql_exception $e) {

            if ($e->getCode() == 1062) {
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }

                $_SESSION['message'] = "Ya existe una experiencia educativa con ese nombre en el mismo programa educativo.";

                return false;
            }
            throw $e;
        }
    }

    public function updateExperiencia($idExperiencia, $nombre, $programa)
    {
        $stmt = $this->conn->prepare("
            UPDATE experiencia_educativa 
            SET nombre = ?, programaEducativo = ? 
            WHERE idExperienciaEducativa = ?
        ");
        $stmt->bind_param("sii", $nombre, $programa, $idExperiencia);
        
        try {
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } catch (mysqli_sql_exception $e) {
            $stmt->close();
            
            if ($e->getCode() == 1062) {
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['message'] = "Ya existe una experiencia educativa con ese nombre en el mismo programa educativo.";
                return false;
            }
            throw $e;
        }
    }

    public function deleteExperiencia($idExperiencia)
    {
        $stmt = $this->conn->prepare("DELETE FROM experiencia_educativa WHERE idExperienciaEducativa = ?");
        $stmt->bind_param("i", $idExperiencia);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function getExperienciasByCarrera($idCarrera)
    {
        $stmt = $this->conn->prepare("SELECT 
            ee.idExperienciaEducativa, 
            ee.nombre
        FROM experiencia_educativa ee 
        WHERE ee.programaEducativo = ?
    ");
        $stmt->bind_param("i", $idCarrera);
        $stmt->execute();
        $result = $stmt->get_result();
        $experiencias = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $experiencias;
    }
    // DEF-03: Verificar si la experiencia educativa tiene datos asociados antes de eliminar
    public function tieneDatosAsociados($idExperiencia)
    {
        // Verificar si tiene problemáticas académicas
        $stmtProblematicas = $this->conn->prepare(
            "SELECT COUNT(*) AS total FROM problematica_academica WHERE experienciaEducativa = ?"
        );
        $stmtProblematicas->bind_param("i", $idExperiencia);
        $stmtProblematicas->execute();
        $resultProblematicas = $stmtProblematicas->get_result();
        $dataProblematicas = $resultProblematicas->fetch_assoc();
        $stmtProblematicas->close();
        
        if ($dataProblematicas['total'] > 0) {
            return true;
        }
        
        // Verificar si tiene secciones
        $stmtSecciones = $this->conn->prepare(
            "SELECT COUNT(*) AS total FROM seccion WHERE idExperienciaEducativa = ?"
        );
        $stmtSecciones->bind_param("i", $idExperiencia);
        $stmtSecciones->execute();
        $resultSecciones = $stmtSecciones->get_result();
        $dataSecciones = $resultSecciones->fetch_assoc();
        $stmtSecciones->close();
        
        return $dataSecciones['total'] > 0;
    }
}