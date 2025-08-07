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
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function updateExperiencia($idExperiencia, $nombre, $programa)
    {
        $stmt = $this->conn->prepare("
            UPDATE experiencia_educativa 
            SET nombre = ?, programaEducativo = ? 
            WHERE idExperienciaEducativa = ?
        ");
        $stmt->bind_param("sii", $nombre, $programa, $idExperiencia);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
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
}