<?php
class Administrador
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAdministradores($currentAdminId)
    {
        $stmt = $this->conn->prepare("
            SELECT a.idAdministrador, 
                   CONCAT(a.nombre, ' ', COALESCE(a.apellidoPaterno, ''), ' ', COALESCE(a.apellidoMaterno, '')) AS administradorNombre, 
                   a.correoInstitucional, 
                   a.sesion 
            FROM administrador a
            WHERE a.idAdministrador != ?
        ");
        $stmt->bind_param("i", $currentAdminId);
        $stmt->execute();
        $result = $stmt->get_result();
        $administradores = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $administradores;
    }

    public function getAdministradorById($idAdministrador)
    {
        $stmt = $this->conn->prepare("
            SELECT a.idAdministrador, 
                   a.nombre, 
                   a.apellidoPaterno, 
                   a.apellidoMaterno, 
                   a.correoInstitucional, 
                   a.sesion
            FROM administrador a
            WHERE a.idAdministrador = ?
        ");
        $stmt->bind_param("i", $idAdministrador);
        $stmt->execute();
        $result = $stmt->get_result();
        $administrador = $result->fetch_assoc();
        $stmt->close();
        return $administrador;
    }

    public function createAdministrador($data)
    {
        $this->conn->begin_transaction();

        try {
            // Inserción en la tabla sesion
            $stmtSesion = $this->conn->prepare("INSERT INTO sesion (correoInstitucional, rol) VALUES (?, ?)");
            $stmtSesion->bind_param("si", $data['correoInstitucional'], $data['rol']);
            $stmtSesion->execute();
            $idSesion = $this->conn->insert_id;
            $stmtSesion->close();

            // Inserción en la tabla administrador
            $stmtAdministrador = $this->conn->prepare("
            INSERT INTO administrador (nombre, apellidoPaterno, apellidoMaterno, correoInstitucional, sesion, password)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
            $stmtAdministrador->bind_param(
                "ssssss",
                $data['nombre'],
                $data['apellidoPaterno'],
                $data['apellidoMaterno'],
                $data['correoInstitucional'],
                $idSesion,
                $data['password'] // Asegúrate de que este campo exista como 'password', no 'hashedPassword'
            );
            $stmtAdministrador->execute();
            $stmtAdministrador->close();

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollback();
            return $e->getMessage(); // Retornamos el mensaje del error.
        }
    }

    public function updateAdministrador($idAdministrador, $data)
    {
        $this->conn->begin_transaction();
        try {
            $stmtAdministrador = $this->conn->prepare("
            UPDATE administrador 
            SET nombre = ?, apellidoPaterno = ?, apellidoMaterno = ?, correoInstitucional = ?
            WHERE idAdministrador = ?
        ");

            $stmtAdministrador->bind_param(
                "ssssi",
                $data['nombre'],
                $data['apellidoPaterno'],
                $data['apellidoMaterno'],
                $data['correoInstitucional'],
                $idAdministrador
            );
            $stmtAdministrador->execute();
            $stmtAdministrador->close();

            if (!empty($data['password'])) {
                $stmtPassword = $this->conn->prepare("
                UPDATE administrador
                SET password = ?
                WHERE idAdministrador = ?
            ");
                $stmtPassword->bind_param("si", $data['password'], $idAdministrador);
                $stmtPassword->execute();
                $stmtPassword->close();
            }

            $stmtSesion = $this->conn->prepare("
            UPDATE sesion 
            SET correoInstitucional = ? 
            WHERE idSesion = (
                SELECT sesion FROM administrador WHERE idAdministrador = ?
            )
        ");
            $stmtSesion->bind_param("si", $data['correoInstitucional'], $idAdministrador);
            $stmtSesion->execute();
            $stmtSesion->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function deleteAdministrador($idAdministrador)
    {
        $this->conn->begin_transaction();
        try {
            $stmtSesion = $this->conn->prepare("
                SELECT sesion FROM administrador WHERE idAdministrador = ?
            ");
            $stmtSesion->bind_param("i", $idAdministrador);
            $stmtSesion->execute();
            $result = $stmtSesion->get_result();
            $sesionData = $result->fetch_assoc();
            $idSesion = $sesionData['sesion'];
            $stmtSesion->close();

            $stmtAdministrador = $this->conn->prepare("DELETE FROM administrador WHERE idAdministrador = ?");
            $stmtAdministrador->bind_param("i", $idAdministrador);
            $stmtAdministrador->execute();
            $stmtAdministrador->close();

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

    public function getAdministradorByCorreo($correoInstitucional)
    {
        $stmt = $this->conn->prepare("
        SELECT a.*, s.rol 
        FROM administrador a
        INNER JOIN sesion s ON a.sesion = s.idSesion
        WHERE a.correoInstitucional = ?
    ");
        $stmt->bind_param("s", $correoInstitucional);
        $stmt->execute();
        $result = $stmt->get_result();
        $administrador = $result->fetch_assoc();
        $stmt->close();
        return $administrador;
    }

}