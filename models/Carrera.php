<?php
class Carrera
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getCarreras()
    {
        $stmt = $this->conn->prepare("SELECT idCarrera, nombre AS carrera FROM carrera");
        $stmt->execute();
        $result = $stmt->get_result();
        $carreras = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $carreras;
    }

    public function getCarreraById($idCarrera)
    {
        $stmt = $this->conn->prepare("SELECT idCarrera, nombre AS carrera FROM carrera WHERE idCarrera = ?");
        $stmt->bind_param("i", $idCarrera);
        $stmt->execute();
        $result = $stmt->get_result();
        $carrera = $result->fetch_assoc();
        $stmt->close();
        return $carrera;
    }

    public function createCarrera($nombreCarrera)
    {
        $stmt = $this->conn->prepare("INSERT INTO carrera (nombre) VALUES (?)");
        $stmt->bind_param("s", $nombreCarrera);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function updateCarrera($idCarrera, $nombreCarrera)
    {
        $stmt = $this->conn->prepare("UPDATE carrera SET nombre = ? WHERE idCarrera = ?");
        $stmt->bind_param("si", $nombreCarrera, $idCarrera);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function deleteCarrera($idCarrera)
    {
        $stmt = $this->conn->prepare("DELETE FROM carrera WHERE idCarrera = ?");
        $stmt->bind_param("i", $idCarrera);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

}