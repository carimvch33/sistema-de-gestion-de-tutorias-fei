<?php
class PeriodoTutorias
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getPeriodosByCarrera($idCarrera)
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
}
?>