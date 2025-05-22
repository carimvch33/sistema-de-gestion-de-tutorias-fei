<?php
class PeriodoTutorias
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getPeriodosTutoriasByCarrera($idCarrera)
    {
        $stmt = $this->conn->prepare("SELECT 
                pt.idPeriodoTutorias,
                pt.fechaInicio,
                pt.fechaFin,
                pt.numSesion
            FROM periodo_tutorias pt
            INNER JOIN carrera c ON c.idCarrera = pt.carrera
            INNER JOIN periodo p ON p.idPeriodo = pt.periodo
            WHERE p.actual = true AND pt.carrera = ?;
        ");
        $stmt->bind_param("i", $idCarrera);
        $stmt->execute();
        $result = $stmt->get_result();
        $fechas = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $fechas;
    }

    public function getPeriodoTutoriasById($idPeriodoTutorias)
    {
        $stmt = $this->conn->prepare("SELECT 
                pt.idPeriodoTutorias,
                pt.fechaInicio,
                pt.fechaFin,
                pt.numSesion,
                pt.carrera,
                pt.periodo
            FROM periodo_tutorias pt
            WHERE pt.idPeriodoTutorias = ?;
        ");
        $stmt->bind_param("i", $idPeriodoTutorias);
        $stmt->execute();
        $result = $stmt->get_result();
        $fechas = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $fechas;
    }
}
?>