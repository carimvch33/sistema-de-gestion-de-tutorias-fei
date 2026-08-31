<?php
class JefeCarrera
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getJefesCarrera()
    {
        $stmt = $this->conn->prepare("
            SELECT t.idTutor,
                   CONCAT(t.nombre, ' ', COALESCE(t.apellidoPaterno, ''), ' ', COALESCE(t.apellidoMaterno, '')) AS tutorNombre,
                   t.noPersonal,
                   t.correoInstitucional,
                   t.sesion,
                   GROUP_CONCAT(c.nombre SEPARATOR ', ') AS carreraNombre
            FROM tutor t
            INNER JOIN sesion s ON s.correoInstitucional = t.correoInstitucional
            LEFT JOIN jefe_carrera_carrera jcc ON s.idSesion = jcc.idSesion
            LEFT JOIN carrera c ON jcc.idCarrera = c.idCarrera
            WHERE s.rol = 5
            GROUP BY t.idTutor
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $jefesCarrera = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $jefesCarrera;
    }

    public function isCarreraAsignada($carrerasArray, $idTutorExcluir = 0)
    {
        foreach ($carrerasArray as $idCarrera) {
            $id = intval($idCarrera);
            if ($idTutorExcluir > 0) {
                $stmt = $this->conn->prepare("
                    SELECT COUNT(*) as total 
                    FROM jefe_carrera_carrera jcc
                    INNER JOIN sesion s ON jcc.idSesion = s.idSesion
                    INNER JOIN tutor t ON s.idSesion = t.sesion
                    WHERE jcc.idCarrera = ? AND t.idTutor != ?
                ");
                $stmt->bind_param("ii", $id, $idTutorExcluir);
            } else {
                $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM jefe_carrera_carrera WHERE idCarrera = ?");
                $stmt->bind_param("i", $id);
            }
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($result['total'] > 0) {
                return true; // Ya está ocupada
            }
        }
        return false;
    }

    public function createJefeCarrera($data)
    {
        $this->conn->begin_transaction();
        try {
            $stmtSesion = $this->conn->prepare("INSERT INTO sesion (correoInstitucional, rol) VALUES (?, ?)");
            $stmtSesion->bind_param("si", $data['correoInstitucional'], $data['rol']);
            if (!$stmtSesion->execute()) {
                if ($stmtSesion->errno == 1062) throw new Exception("1062");
                throw new Exception("Error en sesion");
            }
            $idSesion = $this->conn->insert_id;
            $stmtSesion->close();

            $stmtJefe = $this->conn->prepare("
                INSERT INTO tutor (nombre, apellidoPaterno, apellidoMaterno, noPersonal, correoInstitucional, sesion)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmtJefe->bind_param("sssssi", $data['nombre'], $data['apellidoPaterno'], $data['apellidoMaterno'], $data['noPersonal'], $data['correoInstitucional'], $idSesion);
            if (!$stmtJefe->execute()) {
                if ($stmtJefe->errno == 1062) throw new Exception("1062");
                throw new Exception("Error en tutor");
            }
            $stmtJefe->close();

            $stmtRelacion = $this->conn->prepare("INSERT INTO jefe_carrera_carrera (idSesion, idCarrera) VALUES (?, ?)");
            foreach ($data['carreras'] as $idCarrera) {
                $id = intval($idCarrera);
                $stmtRelacion->bind_param("ii", $idSesion, $id);
                $stmtRelacion->execute();
            }
            $stmtRelacion->close();

            $this->conn->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->conn->rollback();
            if ($e->getCode() == 1062) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['message'] = 'El correo institucional o el número de personal ya están registrados.';
            }
            return false;
        } catch (Exception $e) {
            $this->conn->rollback();
            if (strpos($e->getMessage(), '1062') !== false) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['message'] = 'El correo institucional o el número de personal ya están registrados.';
            }
            return false;
        }
    }

    public function getJefeCarreraById($idTutor)
    {
        $stmt = $this->conn->prepare("
            SELECT t.idTutor, t.nombre, t.apellidoPaterno, t.apellidoMaterno, t.noPersonal, t.correoInstitucional, s.rol, s.idSesion
            FROM tutor t
            INNER JOIN sesion s ON s.idSesion = t.sesion
            WHERE t.idTutor = ?
        ");
        $stmt->bind_param("i", $idTutor);
        $stmt->execute();
        $jefe = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($jefe) {
            $stmtCarreras = $this->conn->prepare("SELECT idCarrera FROM jefe_carrera_carrera WHERE idSesion = ?");
            $stmtCarreras->bind_param("i", $jefe['idSesion']);
            $stmtCarreras->execute();
            $resCarreras = $stmtCarreras->get_result();
            $carreras = [];
            while ($row = $resCarreras->fetch_assoc()) {
                $carreras[] = $row['idCarrera'];
            }
            $stmtCarreras->close();
            $jefe['carreras'] = $carreras;
        }
        return $jefe;
    }

    public function updateJefeCarrera($idTutor, $data)
    {
        $this->conn->begin_transaction();
        try {
            $stmtTutor = $this->conn->prepare("
                UPDATE tutor SET nombre = ?, apellidoPaterno = ?, apellidoMaterno = ?, noPersonal = ?, correoInstitucional = ? WHERE idTutor = ?
            ");
            $stmtTutor->bind_param("sssssi", $data['nombre'], $data['apellidoPaterno'], $data['apellidoMaterno'], $data['noPersonal'], $data['correoInstitucional'], $idTutor);
            if (!$stmtTutor->execute()) {
                 if ($stmtTutor->errno == 1062) throw new Exception("1062");
                 throw new Exception("Error al actualizar tutor");
            }
            $stmtTutor->close();

            $stmtSesionId = $this->conn->prepare("SELECT sesion FROM tutor WHERE idTutor = ?");
            $stmtSesionId->bind_param("i", $idTutor);
            $stmtSesionId->execute();
            $idSesion = $stmtSesionId->get_result()->fetch_assoc()['sesion'];
            $stmtSesionId->close();

            $stmtSesion = $this->conn->prepare("UPDATE sesion SET correoInstitucional = ? WHERE idSesion = ?");
            $stmtSesion->bind_param("si", $data['correoInstitucional'], $idSesion);
            if (!$stmtSesion->execute()) {
                 if ($stmtSesion->errno == 1062) throw new Exception("1062");
                 throw new Exception("Error al actualizar sesion");
            }
            $stmtSesion->close();

            $stmtDel = $this->conn->prepare("DELETE FROM jefe_carrera_carrera WHERE idSesion = ?");
            $stmtDel->bind_param("i", $idSesion);
            $stmtDel->execute();
            $stmtDel->close();

            $stmtIns = $this->conn->prepare("INSERT INTO jefe_carrera_carrera (idSesion, idCarrera) VALUES (?, ?)");
            foreach ($data['carreras'] as $idCarrera) {
                $id = intval($idCarrera);
                $stmtIns->bind_param("ii", $idSesion, $id);
                $stmtIns->execute();
            }
            $stmtIns->close();

            $this->conn->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->conn->rollback();
            if ($e->getCode() == 1062) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['message'] = 'El correo institucional o el número de personal ya están registrados.';
            }
            return false;
        } catch (Exception $e) {
            $this->conn->rollback();
            if (strpos($e->getMessage(), '1062') !== false) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['message'] = 'El correo institucional o el número de personal ya están registrados.';
            }
            return false;
        }
    }

    public function deleteJefeCarrera($idTutor)
    {
        $this->conn->begin_transaction();
        try {
            $stmtSesion = $this->conn->prepare("SELECT sesion FROM tutor WHERE idTutor = ?");
            $stmtSesion->bind_param("i", $idTutor);
            $stmtSesion->execute();
            $idSesion = $stmtSesion->get_result()->fetch_assoc()['sesion'];
            $stmtSesion->close();

            $stmtTutor = $this->conn->prepare("DELETE FROM tutor WHERE idTutor = ?");
            $stmtTutor->bind_param("i", $idTutor);
            $stmtTutor->execute();
            $stmtTutor->close();

            $stmtSesionDel = $this->conn->prepare("DELETE FROM sesion WHERE idSesion = ?");
            $stmtSesionDel->bind_param("i", $idSesion);
            $stmtSesionDel->execute();
            $stmtSesionDel->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
}
?>