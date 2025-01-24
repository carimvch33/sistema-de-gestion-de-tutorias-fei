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
                   t.sesion
            FROM tutor t
            INNER JOIN sesion s ON s.correoInstitucional = t.correoInstitucional
            WHERE s.rol = 5
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $jefesCarrera = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $jefesCarrera;
    }

    public function createJefeCarrera($data)
    {
        $this->conn->begin_transaction();

        try {
            $stmtSesion = $this->conn->prepare("INSERT INTO sesion (correoInstitucional, rol) VALUES (?, ?)");
            $stmtSesion->bind_param("si", $data['correoInstitucional'], $data['rol']);
            $stmtSesion->execute();
            $idSesion = $this->conn->insert_id;
            $stmtSesion->close();

            $stmtJefeCarrera = $this->conn->prepare("
                INSERT INTO tutor (nombre, apellidoPaterno, apellidoMaterno, noPersonal, correoInstitucional, sesion)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmtJefeCarrera->bind_param(
                "sssssi",
                $data['nombre'],
                $data['apellidoPaterno'],
                $data['apellidoMaterno'],
                $data['noPersonal'],
                $data['correoInstitucional'],
                $idSesion
            );
            $stmtJefeCarrera->execute();
            $stmtJefeCarrera->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
}