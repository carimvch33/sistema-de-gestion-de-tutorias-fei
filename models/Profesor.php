<?php
class Profesor
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getProfesores()
    {
        $stmt = $this->conn->prepare("
            SELECT t.idTutor, 
                   CONCAT(t.nombre, ' ', COALESCE(t.apellidoPaterno, ''), ' ', COALESCE(t.apellidoMaterno, '')) AS tutorNombre, 
                   t.noPersonal, 
                   t.correoInstitucional, 
                   t.sesion 
            FROM tutor t
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $profesores = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $profesores;
    }

    public function getTutors()
    {
        $stmt = $this->conn->prepare("SELECT t.idTutor, CONCAT(t.nombre, ' ', COALESCE(t.apellidoPaterno, ''), ' ', COALESCE(t.apellidoMaterno, '')) AS profesorNombre FROM tutor t");
        $stmt->execute();
        $result = $stmt->get_result();
        $tutors = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $tutors;
    }

    public function getProfesorById($idTutor)
    {
        $stmt = $this->conn->prepare("
            SELECT t.idTutor, 
                   t.nombre, 
                   t.apellidoPaterno, 
                   t.apellidoMaterno, 
                   t.noPersonal, 
                   t.correoInstitucional, 
                   t.sesion, 
                   s.rol 
            FROM tutor t
            INNER JOIN sesion s ON s.idSesion = t.sesion
            WHERE t.idTutor = ?
        ");
        $stmt->bind_param("i", $idTutor);
        $stmt->execute();
        $result = $stmt->get_result();
        $profesor = $result->fetch_assoc();
        $stmt->close();
        return $profesor;
    }

    public function createProfesor($data)
    {
        $this->conn->begin_transaction();

        try {
            $stmtSesion = $this->conn->prepare("INSERT INTO sesion (correoInstitucional, rol) VALUES (?, ?)");
            $stmtSesion->bind_param("si", $data['correoInstitucional'], $data['rol']);
            $stmtSesion->execute();
            $idSesion = $this->conn->insert_id;
            $stmtSesion->close();

            $stmtProfesor = $this->conn->prepare("
                INSERT INTO tutor (nombre, apellidoPaterno, apellidoMaterno, noPersonal, correoInstitucional, sesion)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmtProfesor->bind_param(
                "sssssi",
                $data['nombre'],
                $data['apellidoPaterno'],
                $data['apellidoMaterno'],
                $data['noPersonal'],
                $data['correoInstitucional'],
                $idSesion
            );
            $stmtProfesor->execute();
            $stmtProfesor->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function updateProfesor($idTutor, $data)
    {
        $this->conn->begin_transaction();
        try {
            $stmtProfesor = $this->conn->prepare("
                UPDATE tutor 
                SET nombre = ?, apellidoPaterno = ?, apellidoMaterno = ?, noPersonal = ?, correoInstitucional = ? 
                WHERE idTutor = ?
            ");
            $stmtProfesor->bind_param(
                "sssssi",
                $data['nombre'],
                $data['apellidoPaterno'],
                $data['apellidoMaterno'],
                $data['noPersonal'],
                $data['correoInstitucional'],
                $idTutor
            );
            $stmtProfesor->execute();
            $stmtProfesor->close();

            $stmtSesion = $this->conn->prepare("
                UPDATE sesion 
                SET correoInstitucional = ? 
                WHERE idSesion = (
                    SELECT sesion FROM tutor WHERE idTutor = ?
                )
            ");
            $stmtSesion->bind_param("si", $data['correoInstitucional'], $idTutor);
            $stmtSesion->execute();
            $stmtSesion->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function deleteProfesor($idTutor)
    {
        $this->conn->begin_transaction();
        try {
            $stmtSesion = $this->conn->prepare("
                SELECT sesion FROM tutor WHERE idTutor = ?
            ");
            $stmtSesion->bind_param("i", $idTutor);
            $stmtSesion->execute();
            $result = $stmtSesion->get_result();
            $sesionData = $result->fetch_assoc();
            $idSesion = $sesionData['sesion'];
            $stmtSesion->close();

            $stmtProfesor = $this->conn->prepare("DELETE FROM tutor WHERE idTutor = ?");
            $stmtProfesor->bind_param("i", $idTutor);
            $stmtProfesor->execute();
            $stmtProfesor->close();

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

    public function getTutorsWithSession()
    {
        $stmt = $this->conn->prepare("SELECT t.idTutor, CONCAT(t.nombre, ' ', COALESCE(t.apellidoPaterno, ''), ' ', COALESCE(t.apellidoMaterno, '')) AS tutorNombre, t.sesion FROM tutor t ORDER BY tutorNombre ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        $tutors = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $tutors;
    }

    public function updateTutorRole($sesionId, $nuevoRol)
    {
        if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            return false;
        }

        $stmt = $this->conn->prepare("UPDATE sesion SET rol = ? WHERE idSesion = ?");
        $stmt->bind_param("ii", $nuevoRol, $sesionId);

        if ($stmt->execute()) {
            $resultado = true;
        } else {
            $resultado = false;
        }

        $stmt->close();
        return $resultado;
    }

    public function getIdTutorByCorreo($correoInstitucional)
    {
        $stmt = $this->conn->prepare("SELECT idTutor FROM tutor WHERE correoInstitucional = ?");
        $stmt->bind_param("s", $correoInstitucional);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows === 0) {
            return null;
        }

        $row = $result->fetch_assoc();
        return $row['idTutor'];
    }

    public function getProfesoresByCarrera($idCarrera)
    {
        $stmt = $this->conn->prepare("
        SELECT t.idTutor AS idProfesor, 
               CONCAT(t.nombre, ' ', COALESCE(t.apellidoPaterno, ''), ' ', COALESCE(t.apellidoMaterno, '')) AS nombreProfesor
        FROM tutor t
        INNER JOIN experiencia_educativa ee ON ee.profesor = t.idTutor
        WHERE ee.programaEducativo = ?
        GROUP BY t.idTutor
    ");
        $stmt->bind_param("i", $idCarrera);
        $stmt->execute();
        $result = $stmt->get_result();
        $profesores = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $profesores;
    }

    public function getTutorBySesionId($sesionId)
    {
        $stmt = $this->conn->prepare("
        SELECT t.idTutor, t.correoInstitucional
        FROM tutor t
        WHERE t.sesion = ?
    ");
        $stmt->bind_param("i", $sesionId);
        $stmt->execute();
        $result = $stmt->get_result();
        $profesor = $result->fetch_assoc();
        $stmt->close();

        return $profesor;
    }

    public function isProfessorRegistered($correoInstitucional)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM tutor WHERE correoInstitucional = ?");
        $stmt->bind_param("s", $correoInstitucional);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();

        return $data['total'] > 0;
    }
}