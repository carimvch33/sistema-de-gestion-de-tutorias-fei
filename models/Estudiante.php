<?php
class Estudiante
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getEstudiantes()
    {
        $stmt = $this->conn->prepare("
            SELECT t.idTutorado, 
                   CONCAT(t.nombre, ' ', COALESCE(t.apellidoPaterno, ''), ' ', COALESCE(t.apellidoMaterno, '')) AS tutoradoNombre, 
                   t.matricula, 
                   t.correoInstitucional, 
                   c.nombre AS carrera, 
                   CONCAT(tt.nombre, ' ', COALESCE(tt.apellidoPaterno, ''), ' ', COALESCE(tt.apellidoMaterno, '')) AS tutorNombre, 
                   t.sesion 
            FROM tutorado t
            INNER JOIN carrera c ON c.idCarrera = t.carrera
            LEFT JOIN tutor tt ON tt.idTutor = t.tutor
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $estudiantes = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $estudiantes;
    }

    public function getEstudianteById($idTutorado)
    {
        $stmt = $this->conn->prepare("
            SELECT t.idTutorado, 
                   t.nombre, 
                   t.apellidoPaterno, 
                   t.apellidoMaterno, 
                   t.matricula, 
                   t.correoInstitucional, 
                   t.carrera, 
                   t.tutor, 
                   t.sesion 
            FROM tutorado t
            WHERE t.idTutorado = ?
        ");
        $stmt->bind_param("i", $idTutorado);
        $stmt->execute();
        $result = $stmt->get_result();
        $estudiante = $result->fetch_assoc();
        $stmt->close();
        return $estudiante;
    }

    public function createEstudiante($data)
    {
        $this->conn->begin_transaction();

        try {
            $stmtSesion = $this->conn->prepare("INSERT INTO sesion (correoInstitucional, rol) VALUES (?, ?)");
            $stmtSesion->bind_param("si", $data['correoInstitucional'], $data['rol']);
            
            if (!$stmtSesion->execute()) {
                if ($stmtSesion->errno == 1062) throw new Exception("1062");
                throw new Exception("Error al ejecutar la inserción en sesion");
            }
            
            $idSesion = $this->conn->insert_id;
            $stmtSesion->close();

            $stmtEstudiante = $this->conn->prepare("
                INSERT INTO tutorado (nombre, apellidoPaterno, apellidoMaterno, matricula, carrera, correoInstitucional, sesion, tutor)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $carrera = isset($data['carrera']) ? (int) $data['carrera'] : null;
            $tutor = isset($data['tutor']) ? (int) $data['tutor'] : null;
            $types = "ssssisii"; 
            
            $stmtEstudiante->bind_param($types, $data['nombre'], $data['apellidoPaterno'], $data['apellidoMaterno'], $data['matricula'], $carrera, $data['correoInstitucional'], $idSesion, $tutor);

            if (!$stmtEstudiante->execute()) {
                if ($stmtEstudiante->errno == 1062) throw new Exception("1062");
                throw new Exception("Error al ejecutar la inserción en tutorado");
            }
            $stmtEstudiante->close();

            $this->conn->commit();
            return true;

        } catch (mysqli_sql_exception $e) {
            $this->conn->rollback();
            if ($e->getCode() == 1062) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['message'] = 'La matrícula o el correo institucional ya están registrados.';
                return false;
            }
            return false;
        } catch (Exception $e) {
            $this->conn->rollback();
            if (strpos($e->getMessage(), '1062') !== false) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['message'] = 'La matrícula o el correo institucional ya están registrados.';
                return false;
            }
            return false;
        }
    }

    public function updateEstudiante($idTutorado, $data)
    {
        $this->conn->begin_transaction();

        try {
            $stmtEstudiante = $this->conn->prepare("
                UPDATE tutorado 
                SET nombre = ?, apellidoPaterno = ?, apellidoMaterno = ?, matricula = ?, carrera = ?, correoInstitucional = ?, tutor = ?
                WHERE idTutorado = ?
            ");
            $stmtEstudiante->bind_param("sssssssi", $data['nombre'], $data['apellidoPaterno'], $data['apellidoMaterno'], $data['matricula'], $data['carrera'], $data['correoInstitucional'], $data['tutor'], $idTutorado);
            
            if (!$stmtEstudiante->execute()) {
                if ($stmtEstudiante->errno == 1062) throw new Exception("1062");
                throw new Exception("Error al actualizar tutorado");
            }
            $stmtEstudiante->close();

            $stmtSesion = $this->conn->prepare("
                UPDATE sesion 
                SET correoInstitucional = ? 
                WHERE idSesion = (SELECT sesion FROM tutorado WHERE idTutorado = ?)
            ");
            $stmtSesion->bind_param("si", $data['correoInstitucional'], $idTutorado);
            
            if (!$stmtSesion->execute()) {
                if ($stmtSesion->errno == 1062) throw new Exception("1062");
                throw new Exception("Error al actualizar sesion");
            }
            $stmtSesion->close();

            $this->conn->commit();
            return true;

        } catch (mysqli_sql_exception $e) {
            $this->conn->rollback();
            if ($e->getCode() == 1062) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['message'] = 'La matrícula o el correo institucional ya están registrados.';
                return false;
            }
            return false;
        } catch (Exception $e) {
            $this->conn->rollback();
            if (strpos($e->getMessage(), '1062') !== false) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['message'] = 'La matrícula o el correo institucional ya están registrados.';
                return false;
            }
            return false;
        }
    }

    public function deleteEstudiante($idTutorado)
    {
        $this->conn->begin_transaction();

        try {
            $stmtSesion = $this->conn->prepare("
                SELECT sesion FROM tutorado WHERE idTutorado = ?
            ");
            $stmtSesion->bind_param("i", $idTutorado);
            $stmtSesion->execute();
            $result = $stmtSesion->get_result();
            $sesionData = $result->fetch_assoc();
            $idSesion = $sesionData['sesion'];
            $stmtSesion->close();

            $stmtEstudiante = $this->conn->prepare("DELETE FROM tutorado WHERE idTutorado = ?");
            $stmtEstudiante->bind_param("i", $idTutorado);
            $stmtEstudiante->execute();
            $stmtEstudiante->close();

            $stmtSesionDelete = $this->conn->prepare("DELETE FROM sesion WHERE idSesion = ?");
            $stmtSesionDelete->bind_param("i", $idSesion);
            $stmtSesionDelete->execute();
            $stmtSesionDelete->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function isStudentRegistered($matricula)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM tutorado WHERE matricula = ?");
        $stmt->bind_param("s", $matricula);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        
        return $data['total'] > 0;
    }

    // DEF-02: Verificar si el estudiante tiene tutorías asociadas antes de eliminar
    public function tieneTutoriasAsociadas($idTutorado)
    {
        // Obtener el tutor del estudiante
        $stmtTutor = $this->conn->prepare("SELECT tutor FROM tutorado WHERE idTutorado = ?");
        $stmtTutor->bind_param("i", $idTutorado);
        $stmtTutor->execute();
        $resultTutor = $stmtTutor->get_result();
        $dataTutor = $resultTutor->fetch_assoc();
        $stmtTutor->close();
        
        // Si no tiene tutor asignado, puede eliminarse
        if (!$dataTutor || $dataTutor['tutor'] === null) {
            return false;
        }
        
        // Verificar si ese tutor tiene tutorías registradas
        $tutorId = $dataTutor['tutor'];
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM tutoria WHERE tutor = ?");
        $stmt->bind_param("i", $tutorId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        
        return $data['total'] > 0;
    }
}