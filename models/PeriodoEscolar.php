<?php
class PeriodoEscolar
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getPeriodos()
    {
        $stmt = $this->conn->prepare("SELECT idPeriodo, nombre AS periodo, actual FROM periodo");
        $stmt->execute();
        $result = $stmt->get_result();
        $periodos = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $periodos;
    }

    public function getCurrentPeriodo()
    {
        $stmt = $this->conn->prepare("SELECT idPeriodo, nombre AS periodo FROM periodo WHERE actual = 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $periodo = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $periodo;
    }

    public function getPeriodoById($idPeriodo)
    {
        $stmt = $this->conn->prepare("SELECT idPeriodo, nombre AS periodo, actual FROM periodo WHERE idPeriodo = ?");
        $stmt->bind_param("i", $idPeriodo);
        $stmt->execute();
        $result = $stmt->get_result();
        $periodo = $result->fetch_assoc();
        $stmt->close();
        return $periodo;
    }

    public function createPeriodo($nombre, $actual)
    {
        $this->conn->begin_transaction();

        try {
            $stmt = $this->conn->prepare("INSERT INTO periodo (nombre, actual) VALUES (?, ?)");
            $stmt->bind_param("si", $nombre, $actual);
            $stmt->execute();
            $idPeriodo = $this->conn->insert_id;
            $stmt->close();

            if ($actual == 1) {
                $stmtActual = $this->conn->prepare("UPDATE periodo SET actual = 0 WHERE idPeriodo != ?");
                $stmtActual->bind_param("i", $idPeriodo);
                $stmtActual->execute();
                $stmtActual->close();
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function updatePeriodo($idPeriodo, $nombre, $actual)
    {
        $this->conn->begin_transaction();

        try {
            $stmt = $this->conn->prepare("UPDATE periodo SET nombre = ?, actual = ? WHERE idPeriodo = ?");
            $stmt->bind_param("sii", $nombre, $actual, $idPeriodo);
            $stmt->execute();
            $stmt->close();

            if ($actual == 1) {
                $stmtActual = $this->conn->prepare("UPDATE periodo SET actual = 0 WHERE idPeriodo != ?");
                $stmtActual->bind_param("i", $idPeriodo);
                $stmtActual->execute();
                $stmtActual->close();
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function deletePeriodo($idPeriodo)
    {
        $stmt = $this->conn->prepare("DELETE FROM periodo WHERE idPeriodo = ?");
        $stmt->bind_param("i", $idPeriodo);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function getPeriodoByNombre($nombrePeriodo)
    {
        $stmt = $this->conn->prepare("SELECT idPeriodo, nombre AS periodo, actual FROM periodo WHERE nombre = ?");
        $stmt->bind_param("s", $nombrePeriodo);
        $stmt->execute();
        $result = $stmt->get_result();
        $periodo = $result->fetch_assoc();
        $stmt->close();
        return $periodo;
    }

}