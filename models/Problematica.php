<?php
class Problematica
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getProblematicas()
    {
        $stmt = $this->conn->prepare("
            SELECT p.idProblematica, 
                   p.descripcion, 
                   tp.nombre AS tipoProblematica
            FROM problematica p 
            INNER JOIN tipo_problematica tp ON tp.idTipoProblematica = p.tipoProblematica
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $problematicas = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $problematicas;
    }

    public function getProblematicaById($idProblematica)
    {
        $stmt = $this->conn->prepare("
            SELECT p.idProblematica, 
                   p.descripcion, 
                   p.tipoProblematica
            FROM problematica p 
            WHERE p.idProblematica = ?
        ");
        $stmt->bind_param("i", $idProblematica);
        $stmt->execute();
        $result = $stmt->get_result();
        $problematica = $result->fetch_assoc();
        $stmt->close();
        return $problematica;
    }

    public function createProblematica($descripcion, $tipoProblematica)
    {
        $stmt = $this->conn->prepare("INSERT INTO problematica (descripcion, tipoProblematica) VALUES (?, ?)");
        $stmt->bind_param("si", $descripcion, $tipoProblematica);

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

                $_SESSION['message'] = 'La problemática ya existe. Por favor, ingrese una descripción diferente.';

                return false;
            }
            
            throw $e;
        }
    }

    public function updateProblematica($idProblematica, $descripcion, $tipoProblematica)
    {
        $stmt = $this->conn->prepare("UPDATE problematica SET descripcion = ?, tipoProblematica = ? WHERE idProblematica = ?");
        $stmt->bind_param("sii", $descripcion, $tipoProblematica, $idProblematica);
        
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
                $_SESSION['message'] = 'La problemática ya existe. Por favor, ingrese una descripción diferente.';
                return false;
            }
            
            throw $e;
        }
    }

    public function deleteProblematica($idProblematica)
    {
        $stmt = $this->conn->prepare("DELETE FROM problematica WHERE idProblematica = ?");
        $stmt->bind_param("i", $idProblematica);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function getProblematicasByReporte($idReporte)
    {
        $stmt = $this->conn->prepare("SELECT 
                pa.idProblematicaAcademica, 
                pa.experienciaEducativa, 
                pa.profesor,
                pa.problematica, 
                pa.numAlumnos, 
                pa.estado, 
                pa.otro 
            FROM problematica_academica pa  
            WHERE pa.reporte = ?
        ");
        $stmt->bind_param("i", $idReporte);
        $stmt->execute();
        $result = $stmt->get_result();
        $problematicas = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $problematicas;
    }

    public function deleteProblematicasByReporte($idReporte)
    {
        $stmt = $this->conn->prepare("DELETE FROM problematica_academica WHERE reporte = ?");
        $stmt->bind_param("i", $idReporte);
        $stmt->execute();
        $stmt->close();
    }
}