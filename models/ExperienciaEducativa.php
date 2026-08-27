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
            SELECT 
                ee.idExperienciaEducativa, 
                ee.nombre AS nombreEE, 
                c.nombre AS nombrePrograma,
                s.nrc,
                CONCAT(t.nombre, ' ', COALESCE(t.apellidoPaterno, ''), ' ', COALESCE(t.apellidoMaterno, '')) AS tutorNombre,
                p.nombre AS periodoNombre
            FROM experiencia_educativa ee 
            INNER JOIN carrera c ON c.idCarrera = ee.programaEducativo
            LEFT JOIN seccion s ON s.idExperienciaEducativa = ee.idExperienciaEducativa
            LEFT JOIN tutor t ON s.idProfesor = t.idTutor
            LEFT JOIN periodo p ON s.idPeriodo = p.idPeriodo
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
    public function tieneDatosAsociados($idExperiencia)
    {
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

    public function getExperienciaIntegralById($idExperiencia)
    {
        $stmt = $this->conn->prepare("
            SELECT ee.idExperienciaEducativa, ee.nombre, ee.programaEducativo, 
                   s.id AS idSeccion, s.nrc, s.idProfesor, s.idPeriodo
            FROM experiencia_educativa ee
            LEFT JOIN seccion s ON s.idExperienciaEducativa = ee.idExperienciaEducativa
            WHERE ee.idExperienciaEducativa = ?
        ");
        $stmt->bind_param("i", $idExperiencia);
        $stmt->execute();
        $result = $stmt->get_result();
        $experiencia = $result->fetch_assoc();
        $stmt->close();
        return $experiencia;
    }

    public function updateExperienciaIntegral($idExperiencia, $nombre, $programa, $idSeccion, $nrc, $idProfesor, $idPeriodo)
    {
        $this->conn->begin_transaction();
        try {
            $stmt = $this->conn->prepare("UPDATE experiencia_educativa SET nombre = ?, programaEducativo = ? WHERE idExperienciaEducativa = ?");
            $stmt->bind_param("sii", $nombre, $programa, $idExperiencia);
            if (!$stmt->execute()) { if ($stmt->errno == 1062) throw new Exception("1062_EE"); }
            $stmt->close();

            if (!empty($nrc) && !empty($idProfesor) && !empty($idPeriodo)) {
                if (!empty($idSeccion)) {
                    $stmt2 = $this->conn->prepare("UPDATE seccion SET nrc = ?, idProfesor = ?, idPeriodo = ? WHERE id = ?");
                    $stmt2->bind_param("siii", $nrc, $idProfesor, $idPeriodo, $idSeccion);
                } else {
                    $stmt2 = $this->conn->prepare("INSERT INTO seccion (idProfesor, idExperienciaEducativa, idPeriodo, nrc) VALUES (?, ?, ?, ?)");
                    $stmt2->bind_param("iiis", $idProfesor, $idExperiencia, $idPeriodo, $nrc);
                }
                if (!$stmt2->execute()) { if ($stmt2->errno == 1062) throw new Exception("1062_SEC"); }
                $stmt2->close();
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            if ($e->getMessage() == "1062_EE") $_SESSION['message'] = "Ya existe una materia con ese nombre.";
            else if ($e->getMessage() == "1062_SEC") $_SESSION['message'] = "El NRC ingresado ya está ocupado.";
            else $_SESSION['message'] = "Error al actualizar los datos.";
            return false;
        }
    }

    public function deleteExperienciaIntegral($idExperiencia)
    {
        $this->conn->begin_transaction();
        try {
            $stmt1 = $this->conn->prepare("DELETE FROM seccion WHERE idExperienciaEducativa = ?");
            $stmt1->bind_param("i", $idExperiencia);
            $stmt1->execute();
            $stmt1->close();

            $stmt2 = $this->conn->prepare("DELETE FROM experiencia_educativa WHERE idExperienciaEducativa = ?");
            $stmt2->bind_param("i", $idExperiencia);
            $stmt2->execute();
            $stmt2->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function createExperienciaIntegral($nombre, $programa, $nrc, $idProfesor, $idPeriodo)
    {
        $this->conn->begin_transaction();
        try {
            // 1. Insertar la materia en el catálogo general
            $stmt = $this->conn->prepare("INSERT INTO experiencia_educativa (nombre, programaEducativo) VALUES (?, ?)");
            $stmt->bind_param("si", $nombre, $programa);
            if (!$stmt->execute()) {
                if ($stmt->errno == 1062) throw new Exception("1062_EE");
                throw new Exception("Error al insertar materia");
            }
            $idExperiencia = $this->conn->insert_id;
            $stmt->close();

            // 2. Si el usuario llenó los datos del grupo, creamos la asignación de inmediato
            if (!empty($nrc) && !empty($idProfesor) && !empty($idPeriodo)) {
                $stmt2 = $this->conn->prepare("INSERT INTO seccion (idProfesor, idExperienciaEducativa, idPeriodo, nrc) VALUES (?, ?, ?, ?)");
                $stmt2->bind_param("iiis", $idProfesor, $idExperiencia, $idPeriodo, $nrc);
                if (!$stmt2->execute()) {
                    if ($stmt2->errno == 1062) throw new Exception("1062_SEC");
                    throw new Exception("Error al insertar sección");
                }
                $stmt2->close();
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            if ($e->getMessage() == "1062_EE") {
                $_SESSION['message'] = "Ya existe una experiencia educativa con ese nombre en el mismo programa educativo.";
            } else if ($e->getMessage() == "1062_SEC") {
                $_SESSION['message'] = "El NRC ingresado ya está asignado a otro grupo.";
            } else {
                $_SESSION['message'] = "Error al realizar el registro integral.";
            }
            return false;
        }
    }
}