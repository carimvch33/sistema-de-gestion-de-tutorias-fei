<?php
class TipoProblematica
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getTipos()
    {
        $stmt = $this->conn->prepare("SELECT idTipoProblematica, nombre FROM tipo_problematica");
        $stmt->execute();
        $result = $stmt->get_result();
        $tipos = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $tipos;
    }

    public function getTiposProblematicasById($idTipoProblematica)
    {
        $stmt = $this->conn->prepare("
            SELECT tp.idTipoProblematica, 
                   tp.nombre
            FROM tipo_problematica tp 
            WHERE tp.idTipoProblematica = ?
        ");
        $stmt->bind_param("i", $idTipoProblematica);
        $stmt->execute();
        $result = $stmt->get_result();
        $tipoProblematica = $result->fetch_assoc();
        $stmt->close();
        return $tipoProblematica;
    }

    public function createProblematica($name)
    {
        $stmt = $this->conn->prepare("INSERT INTO tipo_problematica (nombre) VALUES (?)");
        $stmt->bind_param("s", $name);

        try{
            $result = $stmt->execute();
            $stmt->close();
            return $result;

        } catch (mysqli_sql_exception $e) {

            $stmt->close();

            if ($e->getCode() == 1062) {

                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                $_SESSION['message'] = 'La problemática ya existe. Por favor, ingrese un nombre diferente.';

                return false;
            }
            
            throw $e;

        }
    }

    public function updateTipoProblematica($idTipoProblematica, $name)
    {
        $stmt = $this->conn->prepare("UPDATE tipo_problematica SET nombre = ? WHERE idTipoProblematica = ?");
        $stmt->bind_param("si", $name, $idTipoProblematica);
        
        try {
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } catch (mysqli_sql_exception $e) {
            $stmt->close();

            if ($e->getCode() == 1062) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['message'] = 'El tipo de problemática ya existe. Por favor, ingrese un nombre diferente.';
                return false;
            }
            
            throw $e;
        }
    }

    public function deleteProblematica($idTipoProblematica)
    {
        $stmt = $this->conn->prepare("DELETE FROM tipo_problematica WHERE idTipoProblematica = ?");
        $stmt->bind_param("i", $idTipoProblematica);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}