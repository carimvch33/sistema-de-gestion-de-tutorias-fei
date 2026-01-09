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
            // Preparar la inserción en 'sesion'
            $stmtSesion = $this->conn->prepare("INSERT INTO sesion (correoInstitucional, rol) VALUES (?, ?)");
            if (!$stmtSesion) {
                throw new Exception("Error al preparar la inserción en sesion: " . $this->conn->error);
            }
            $stmtSesion->bind_param("si", $data['correoInstitucional'], $data['rol']);
            if (!$stmtSesion->execute()) {
                throw new Exception("Error al ejecutar la inserción en sesion: " . $stmtSesion->error);
            }
            $idSesion = $this->conn->insert_id;
            $stmtSesion->close();

            // Preparar la inserción en 'tutorado'
            $stmtEstudiante = $this->conn->prepare("
            INSERT INTO tutorado (nombre, apellidoPaterno, apellidoMaterno, matricula, carrera, correoInstitucional, sesion, tutor)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
            if (!$stmtEstudiante) {
                throw new Exception("Error al preparar la inserción en tutorado: " . $this->conn->error);
            }

            // Verificar y ajustar los tipos de datos
            $nombre = $data['nombre'] ?? '';
            $apellidoPaterno = $data['apellidoPaterno'] ?? '';
            $apellidoMaterno = $data['apellidoMaterno'] ?? '';
            $matricula = $data['matricula'] ?? '';
            $carrera = $data['carrera'] ?? null;
            $correoInstitucional = $data['correoInstitucional'] ?? '';
            $tutor = $data['tutor'] ?? null;

            // Asegurar que 'carrera' y 'tutor' sean enteros o null
            $carrera = isset($carrera) ? (int) $carrera : null;
            $tutor = isset($tutor) ? (int) $tutor : null;

            // Determinar los tipos de 'bind_param'
            $types = "ssssisii"; // s: string, i: integer
            $stmtEstudiante->bind_param(
                $types,
                $nombre,
                $apellidoPaterno,
                $apellidoMaterno,
                $matricula,
                $carrera,
                $correoInstitucional,
                $idSesion,
                $tutor
            );

            if (!$stmtEstudiante->execute()) {
                throw new Exception("Error al ejecutar la inserción en tutorado: " . $stmtEstudiante->error);
            }
            $stmtEstudiante->close();

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Error al crear tutorado: " . $e->getMessage());
            // Puedes optar por lanzar la excepción nuevamente o devolver false
            // throw $e;
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
            $stmtEstudiante->bind_param(
                "sssssssi",
                $data['nombre'],
                $data['apellidoPaterno'],
                $data['apellidoMaterno'],
                $data['matricula'],
                $data['carrera'],
                $data['correoInstitucional'],
                $data['tutor'],
                $idTutorado
            );
            $stmtEstudiante->execute();
            $stmtEstudiante->close();

            $stmtSesion = $this->conn->prepare("
                UPDATE sesion 
                SET correoInstitucional = ? 
                WHERE idSesion = (
                    SELECT sesion FROM tutorado WHERE idTutorado = ?
                )
            ");
            $stmtSesion->bind_param("si", $data['correoInstitucional'], $idTutorado);
            $stmtSesion->execute();
            $stmtSesion->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
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