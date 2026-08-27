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

    public function createPeriodo($nombre, $actual, $fechas = [])
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

            $stmtCarreras = $this->conn->prepare("SELECT idCarrera FROM carrera");
            $stmtCarreras->execute();
            $resultCarreras = $stmtCarreras->get_result();
            $carreras = $resultCarreras->fetch_all(MYSQLI_ASSOC);
            $stmtCarreras->close();

            if (!empty($carreras) && !empty($fechas)) {
                $stmtSesiones = $this->conn->prepare("INSERT IGNORE INTO periodo_tutorias (numSesion, carrera, periodo, fechaInicio, fechaFin) VALUES (?, ?, ?, ?, ?)");
                
                foreach ($carreras as $carrera) {
                    $idCarrera = $carrera['idCarrera'];
                    
                    foreach ($fechas as $numSesion => $rango) {
                        $fInicio = !empty($rango['inicio']) ? $rango['inicio'] : null;
                        $fFin = !empty($rango['fin']) ? $rango['fin'] : null;
                        
                        $stmtSesiones->bind_param("iiiss", $numSesion, $idCarrera, $idPeriodo, $fInicio, $fFin);
                        $stmtSesiones->execute();
                    }
                }
                $stmtSesiones->close();
            }

            $this->conn->commit();
            return true;
            
        } catch (mysqli_sql_exception $e) {
            $this->conn->rollback();
            if ($e->getCode() == 1062) {
                if (session_status() == PHP_SESSION_NONE) { session_start(); }
                $_SESSION['message'] = "El periodo ya existe.";
                return false;
            }
            throw $e;
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
        } catch (mysqli_sql_exception $e) {
            
            $this->conn->rollback();

            if ($e->getCode() == 1062) {
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['message'] = "El periodo ya existe. Por favor, ingrese un nombre diferente.";
                return false;
            }
            
            throw $e;
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