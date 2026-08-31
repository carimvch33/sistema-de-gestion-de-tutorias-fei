<?php
class Coordinador
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getCoordinadores()
    {
        $stmt = $this->conn->prepare("
            SELECT t.idTutor, 
                   CONCAT(t.nombre, ' ', COALESCE(t.apellidoPaterno, ''), ' ', COALESCE(t.apellidoMaterno, '')) AS tutorNombre, 
                   t.noPersonal, 
                   t.correoInstitucional, 
                   t.sesion 
            FROM tutor t
            INNER JOIN sesion s ON s.correoInstitucional = t.correoInstitucional
            WHERE s.rol = 4
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $coordinadores = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $coordinadores;
    }

    public function getIdSesionByCorreo($correoInstitucional)
    {
        $stmt = $this->conn->prepare("SELECT idSesion FROM sesion WHERE correoInstitucional = ?");
        $stmt->bind_param("s", $correoInstitucional);
        $stmt->execute();
        $result = $stmt->get_result();
        $idSesionRow = $result->fetch_assoc();
        $stmt->close();

        return $idSesionRow ? $idSesionRow['idSesion'] : null;
    }

    public function getCoordinadorById($idTutor)
    {
        $stmt = $this->conn->prepare("
        SELECT t.idTutor, t.nombre, t.apellidoPaterno, t.apellidoMaterno, t.noPersonal, t.correoInstitucional, s.rol, s.idSesion
        FROM tutor t
        INNER JOIN sesion s ON s.idSesion = t.sesion
        WHERE t.idTutor = ?
    ");
        $stmt->bind_param("i", $idTutor);
        $stmt->execute();
        $result = $stmt->get_result();
        $coordinador = $result->fetch_assoc();
        $stmt->close();

        if ($coordinador) {
            $stmtCarreras = $this->conn->prepare("
            SELECT idCarrera
            FROM coordinador_carrera
            WHERE idSesion = ?
        ");
            $stmtCarreras->bind_param("i", $coordinador['idSesion']);
            $stmtCarreras->execute();
            $resultCarreras = $stmtCarreras->get_result();
            $carreras = [];
            while ($row = $resultCarreras->fetch_assoc()) {
                $carreras[] = $row['idCarrera'];
            }
            $stmtCarreras->close();

            $coordinador['carreras'] = $carreras;
        }

        return $coordinador;
    }

    public function createCoordinador($data)
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

            $stmtCoordinador = $this->conn->prepare("
                INSERT INTO tutor (nombre, apellidoPaterno, apellidoMaterno, noPersonal, correoInstitucional, sesion)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmtCoordinador->bind_param(
                "sssssi",
                $data['nombre'],
                $data['apellidoPaterno'],
                $data['apellidoMaterno'],
                $data['noPersonal'],
                $data['correoInstitucional'],
                $idSesion
            );
            
            if (!$stmtCoordinador->execute()) {
                 if ($stmtCoordinador->errno == 1062) throw new Exception("1062");
                 throw new Exception("Error en tutor");
            }
            
            $idTutor = $this->conn->insert_id;
            $stmtCoordinador->close();

            $stmtCoordinadorCarrera = $this->conn->prepare("
                INSERT INTO coordinador_carrera (idSesion, idCarrera)
                VALUES (?, ?)
            ");
            foreach ($data['carreras'] as $idCarrera) {
                $idCarrera = intval($idCarrera);
                $stmtCoordinadorCarrera->bind_param("ii", $idSesion, $idCarrera);
                $stmtCoordinadorCarrera->execute();
            }
            $stmtCoordinadorCarrera->close();

            $this->conn->commit();
            return true;

        } catch (mysqli_sql_exception $e) {
            $this->conn->rollback();
            if ($e->getCode() == 1062) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['message'] = 'El correo institucional o el número de personal ya están registrados. También asegúrate de que las carreras seleccionadas no estén asignadas a otro coordinador.';
                return false;
            }
            error_log("Error de MySQL al crear el coordinador: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            $this->conn->rollback();
            if (strpos($e->getMessage(), '1062') !== false) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['message'] = 'El correo institucional o el número de personal ya están registrados.';
                return false;
            }
            error_log("Error general al crear el coordinador: " . $e->getMessage());
            return false;
        }
    }

    public function updateCoordinador($idTutor, $data)
    {
        $this->conn->begin_transaction();

        try {
            $stmtCoordinador = $this->conn->prepare("
                UPDATE tutor SET nombre = ?, apellidoPaterno = ?, apellidoMaterno = ?, noPersonal = ?, correoInstitucional = ?
                WHERE idTutor = ?
            ");

            $stmtCoordinador->bind_param(
                "sssssi",
                $data['nombre'],
                $data['apellidoPaterno'],
                $data['apellidoMaterno'],
                $data['noPersonal'],
                $data['correoInstitucional'],
                $idTutor
            );
            
            if (!$stmtCoordinador->execute()) {
                 if ($stmtCoordinador->errno == 1062) throw new Exception("1062");
                 throw new Exception("Error al actualizar tutor");
            }
            $stmtCoordinador->close();

            $stmtSesion = $this->conn->prepare("SELECT sesion FROM tutor WHERE idTutor = ?");
            $stmtSesion->bind_param("i", $idTutor);
            $stmtSesion->execute();
            $resultSesion = $stmtSesion->get_result();
            $rowSesion = $resultSesion->fetch_assoc();
            $idSesion = $rowSesion['sesion'];
            $stmtSesion->close();

            $stmtUpdateSesion = $this->conn->prepare("
                UPDATE sesion SET correoInstitucional = ? WHERE idSesion = ?
            ");
            $stmtUpdateSesion->bind_param("si", $data['correoInstitucional'], $idSesion);
            
            if (!$stmtUpdateSesion->execute()) {
                 if ($stmtUpdateSesion->errno == 1062) throw new Exception("1062");
                 throw new Exception("Error al actualizar sesion");
            }
            $stmtUpdateSesion->close();

            $stmtDeleteCarreras = $this->conn->prepare("DELETE FROM coordinador_carrera WHERE idSesion = ?");
            $stmtDeleteCarreras->bind_param("i", $idSesion);
            $stmtDeleteCarreras->execute();
            $stmtDeleteCarreras->close();

            $stmtInsertCarreras = $this->conn->prepare("
                INSERT INTO coordinador_carrera (idSesion, idCarrera)
                VALUES (?, ?)
            ");
            foreach ($data['carreras'] as $idCarrera) {
                $idCarrera = intval($idCarrera);
                $stmtInsertCarreras->bind_param("ii", $idSesion, $idCarrera);
                $stmtInsertCarreras->execute();
            }
            $stmtInsertCarreras->close();

            $this->conn->commit();
            return true;
            
        } catch (mysqli_sql_exception $e) {
            $this->conn->rollback();
            if ($e->getCode() == 1062) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['message'] = 'El correo institucional o el número de personal ya están registrados. También asegúrate de que las carreras seleccionadas no estén asignadas a otro coordinador.';
                return false;
            }
            error_log("Error de MySQL al actualizar el coordinador: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            $this->conn->rollback();
            if (strpos($e->getMessage(), '1062') !== false) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['message'] = 'El correo institucional o el número de personal ya están registrados.';
                return false;
            }
            error_log("Error general al actualizar el coordinador: " . $e->getMessage());
            return false;
        }
    }

    public function deleteCoordinador($idTutor)
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
            $idSesion = $sesionData['sesion'] ?? null;
            $stmtSesion->close();

            if (!$idSesion) {
                throw new Exception("No se encontró la sesión asociada al coordinador.");
            }

            $stmtDeleteCarreras = $this->conn->prepare("DELETE FROM coordinador_carrera WHERE idSesion = ?");
            $stmtDeleteCarreras->bind_param("i", $idSesion);
            $stmtDeleteCarreras->execute();
            $stmtDeleteCarreras->close();

            $stmtCoordinador = $this->conn->prepare("DELETE FROM tutor WHERE idTutor = ?");
            $stmtCoordinador->bind_param("i", $idTutor);
            $stmtCoordinador->execute();
            $stmtCoordinador->close();

            $stmtSesionDelete = $this->conn->prepare("DELETE FROM sesion WHERE idSesion = ?");
            $stmtSesionDelete->bind_param("i", $idSesion);
            $stmtSesionDelete->execute();
            $stmtSesionDelete->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Error al eliminar el coordinador: " . $e->getMessage());
            return false;
        }
    }

    public function assignCarrerasToCoordinador($data)
    {
        try {
            $idSesion = $data['sesionId'];
            $carreras = $data['carreras'];

            if (empty($idSesion) || empty($carreras)) {
                throw new Exception("ID de sesión o carreras no proporcionadas.");
            }

            $stmtInsertCarreras = $this->conn->prepare("INSERT INTO coordinador_carrera (idSesion, idCarrera) VALUES (?, ?)");
            foreach ($carreras as $idCarrera) {
                $idCarrera = intval($idCarrera);
                $stmtInsertCarreras->bind_param("ii", $idSesion, $idCarrera);
                $stmtInsertCarreras->execute();
            }
            $stmtInsertCarreras->close();

            return true;
        } catch (Exception $e) {
            error_log("Error al asignar carreras al coordinador: " . $e->getMessage());
            return false;
        }
    }

    public function deleteCarrerasBySesion($idSesion)
    {
        try {
            $stmtDeleteCarreras = $this->conn->prepare("DELETE FROM coordinador_carrera WHERE idSesion = ?");
            $stmtDeleteCarreras->bind_param("i", $idSesion);
            $stmtDeleteCarreras->execute();
            $stmtDeleteCarreras->close();
            return true;
        } catch (Exception $e) {
            error_log("Error al eliminar las carreras del coordinador: " . $e->getMessage());
            return false;
        }
    }

}