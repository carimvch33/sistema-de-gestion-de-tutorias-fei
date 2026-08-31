<?php
class Seccion
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getSecciones()
    {
        $query = "SELECT s.id, s.nrc, 
                         CONCAT(t.nombre, ' ', COALESCE(t.apellidoPaterno, ''), ' ', COALESCE(t.apellidoMaterno, '')) AS tutorNombre, 
                         ee.nombre AS experiencia, 
                         p.nombre AS periodo
                  FROM seccion s
                  INNER JOIN tutor t ON s.idProfesor = t.idTutor
                  INNER JOIN experiencia_educativa ee ON s.idExperienciaEducativa = ee.idExperienciaEducativa
                  INNER JOIN periodo p ON s.idPeriodo = p.idPeriodo";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $secciones = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $secciones;
    }

    public function createSeccion($idProfesor, $idExperienciaEducativa, $idPeriodo, $nrc)
    {
        $stmt = $this->conn->prepare("INSERT INTO seccion (idProfesor, idExperienciaEducativa, idPeriodo, nrc) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $idProfesor, $idExperienciaEducativa, $idPeriodo, $nrc);

        try{

            $result = $stmt->execute();
            $stmt->close();
            return $result;

        } catch (mysqli_sql_exception $e) {

            $stmt->close();

            if ($e->getCode() == 1062) {

                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                $_SESSION['message'] = 'El NRC ya existe. Por favor, ingrese un NRC diferente.';

                return false;
            }
            
            throw $e;

        }
    }

    public function getSeccionById($idSeccion)
    {
        $query = "SELECT s.id, s.nrc, s.idProfesor, s.idExperienciaEducativa, s.idPeriodo
              FROM seccion s
              WHERE s.id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idSeccion);
        $stmt->execute();
        $result = $stmt->get_result();
        $seccion = $result->fetch_assoc();
        $stmt->close();
        return $seccion;
    }

    public function updateSeccion($idSeccion, $idProfesor, $idExperienciaEducativa, $idPeriodo, $nrc)
    {
        $stmt = $this->conn->prepare("UPDATE seccion SET idProfesor = ?, idExperienciaEducativa = ?, idPeriodo = ?, nrc = ? WHERE id = ?");
        $stmt->bind_param("iiisi", $idProfesor, $idExperienciaEducativa, $idPeriodo, $nrc, $idSeccion);
        
        try {
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } catch (mysqli_sql_exception $e) {
            $stmt->close();

            if ($e->getCode() == 1062) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['message'] = 'El NRC ya existe. Por favor, ingrese un NRC diferente.';
                return false;
            }
            
            throw $e;
        }
    }

    public function deleteSeccion($idSeccion)
    {
        $stmt = $this->conn->prepare("DELETE FROM seccion WHERE id = ?");
        $stmt->bind_param("i", $idSeccion);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function getProfesoresPorExperiencia($idExperiencia, $idCarrera)
    {
        $query = "
        SELECT DISTINCT t.idTutor, CONCAT(t.nombre, ' ', COALESCE(t.apellidoPaterno, ''), ' ', COALESCE(t.apellidoMaterno, '')) AS tutorNombre
        FROM seccion s
        INNER JOIN tutor t ON s.idProfesor = t.idTutor
        INNER JOIN experiencia_educativa ee ON s.idExperienciaEducativa = ee.idExperienciaEducativa
        WHERE s.idExperienciaEducativa = ? AND ee.programaEducativo = ?
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $idExperiencia, $idCarrera);
        $stmt->execute();
        $result = $stmt->get_result();
        $profesores = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $profesores;
    }

    public function getExperienciasPorProfesor($idProfesor, $idCarrera)
    {
        $query = "
        SELECT DISTINCT ee.idExperienciaEducativa, ee.nombre
        FROM seccion s
        INNER JOIN experiencia_educativa ee ON s.idExperienciaEducativa = ee.idExperienciaEducativa
        INNER JOIN tutor t ON s.idProfesor = t.idTutor
        WHERE s.idProfesor = ? AND ee.programaEducativo = ?
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $idProfesor, $idCarrera);
        $stmt->execute();
        $result = $stmt->get_result();
        $experiencias = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $experiencias;
    }

    public function getSeccionesByCarrera($idCarrera)
    {
        $query = "
        SELECT s.id, s.idProfesor, s.idExperienciaEducativa
        FROM seccion s
        INNER JOIN experiencia_educativa ee ON s.idExperienciaEducativa = ee.idExperienciaEducativa
        WHERE ee.programaEducativo = ?
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idCarrera);
        $stmt->execute();
        $result = $stmt->get_result();
        $secciones = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $secciones;
    }

}
?>