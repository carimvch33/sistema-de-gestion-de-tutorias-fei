<?php
class Reporte
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getReportesByTutor($correoInstitucional)
    {
        $stmt = $this->conn->prepare("SELECT  rt.idReporte, 
                                            c.nombre AS carrera, 
                                            p.nombre AS periodo, 
                                            rt.fechaInicioTutoria, 
                                            rt.fechaFinTutoria, 
                                            rt.numTutoria, 
                                            rt.numRiesgo, 
                                            rt.comentario,
                                            rt.esBorrador,
                                            (SELECT COUNT(pa.idProblematicaAcademica) FROM problematica_academica pa WHERE pa.reporte = rt.idReporte) AS tieneProblematica, 
                                            rt.fechaCreacion 
                                    FROM 
                                        reporte_tutoria rt 
                                    INNER JOIN carrera_tutor tc ON tc.idCarreraTutor = rt.carreraTutor 
                                    INNER JOIN carrera c ON c.idCarrera = tc.carrera 
                                    INNER JOIN tutor t ON t.idTutor = tc.tutor 
                                    INNER JOIN periodo p ON p.idPeriodo = rt.periodo 
                                    WHERE 
                                        t.correoInstitucional = ? AND p.actual = 1
                                    ORDER BY 
                                        STR_TO_DATE(p.nombre, '%M %Y - %M %Y') DESC");

        $stmt->bind_param("s", $correoInstitucional);
        $stmt->execute();
        $result = $stmt->get_result();

        $reportes = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $reportes;
    }

    public function getReportHistoryByTutor($correoInstitucional)
    {
        $stmt = $this->conn->prepare("SELECT  rt.idReporte, 
                                            c.nombre AS carrera, 
                                            p.nombre AS periodo, 
                                            rt.fechaInicioTutoria, 
                                            rt.fechaFinTutoria, 
                                            rt.numTutoria, 
                                            rt.numRiesgo, 
                                            rt.comentario,
                                            rt.esBorrador,
                                            (SELECT COUNT(pa.idProblematicaAcademica) FROM problematica_academica pa WHERE pa.reporte = rt.idReporte) AS tieneProblematica, 
                                            rt.fechaCreacion 
                                    FROM 
                                        reporte_tutoria rt 
                                    INNER JOIN carrera_tutor tc ON tc.idCarreraTutor = rt.carreraTutor 
                                    INNER JOIN carrera c ON c.idCarrera = tc.carrera 
                                    INNER JOIN tutor t ON t.idTutor = tc.tutor 
                                    INNER JOIN periodo p ON p.idPeriodo = rt.periodo 
                                    WHERE 
                                        t.correoInstitucional = ? AND p.actual = 0 AND rt.esBorrador = 0
                                    ORDER BY 
                                        STR_TO_DATE(p.nombre, '%M %Y - %M %Y') DESC");

        $stmt->bind_param("s", $correoInstitucional);
        $stmt->execute();
        $result = $stmt->get_result();

        $reportes = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $reportes;
    }

    public function getReportesByCoordinador($idSesion)
    {
        $stmt = $this->conn->prepare("SELECT 
            rt.idReporte,
            CONCAT(t.nombre, ' ', COALESCE(t.apellidoPaterno, ''), ' ', COALESCE(t.apellidoMaterno, '')) as tutorNombre,
            c.nombre AS carrera,
            p.nombre AS periodo,
            rt.numTutoria,
            rt.fechaInicioTutoria,
            rt.fechaFinTutoria,
            rt.numRiesgo,
            rt.comentario,
            (SELECT COUNT(pa.idProblematicaAcademica) FROM problematica_academica pa WHERE pa.reporte = rt.idReporte) AS tieneProblematica,
            rt.fechaCreacion
        FROM reporte_tutoria rt
            INNER JOIN carrera_tutor ct ON ct.idCarreraTutor = rt.carreraTutor
            INNER JOIN carrera c ON ct.carrera = c.idCarrera
            INNER JOIN tutor t ON t.idTutor = ct.tutor
            INNER JOIN coordinador_carrera cc ON cc.idCarrera = c.idCarrera
            INNER JOIN periodo p ON p.idPeriodo = rt.periodo
        WHERE 
            cc.idSesion = ? AND rt.esBorrador = 0 AND p.actual = 1
        ORDER BY 
            STR_TO_DATE(SUBSTRING_INDEX(p.nombre, ' - ', 1), '%M %Y') DESC;
        ");
        $stmt->bind_param("i", $idSesion);
        $stmt->execute();
        $result = $stmt->get_result();

        $reportes = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $reportes;
    }

    public function getHistorialReportesByCoordinador($idSesion)
    {
        $stmt = $this->conn->prepare("SELECT 
            rt.idReporte,
            CONCAT(t.nombre, ' ', COALESCE(t.apellidoPaterno, ''), ' ', COALESCE(t.apellidoMaterno, '')) as tutorNombre,
            c.nombre AS carrera,
            p.nombre AS periodo,
            rt.numTutoria,
            rt.fechaInicioTutoria,
            rt.fechaFinTutoria,
            rt.numRiesgo,
            rt.comentario,
            (SELECT COUNT(pa.idProblematicaAcademica) FROM problematica_academica pa WHERE pa.reporte = rt.idReporte) AS tieneProblematica,
            rt.fechaCreacion
        FROM reporte_tutoria rt
            INNER JOIN carrera_tutor ct ON ct.idCarreraTutor = rt.carreraTutor
            INNER JOIN carrera c ON ct.carrera = c.idCarrera
            INNER JOIN tutor t ON t.idTutor = ct.tutor
            INNER JOIN coordinador_carrera cc ON cc.idCarrera = c.idCarrera
            INNER JOIN periodo p ON p.idPeriodo = rt.periodo
        WHERE 
            cc.idSesion = ? AND rt.esBorrador = 0 AND p.actual = 0
        ORDER BY 
            STR_TO_DATE(SUBSTRING_INDEX(p.nombre, ' - ', 1), '%M %Y') DESC;
        ");
        $stmt->bind_param("i", $idSesion);
        $stmt->execute();
        $result = $stmt->get_result();

        $reportes = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $reportes;
    }

    public function createCarreraTutor($carrera, $idTutor)
    {
        $stmt = $this->conn->prepare("SELECT idCarreraTutor FROM carrera_tutor WHERE carrera = ? AND tutor = ?");
        $stmt->bind_param("ii", $carrera, $idTutor);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $idCarreraTutor = $row['idCarreraTutor'];
        } else {
            $stmtInsert = $this->conn->prepare("INSERT INTO carrera_tutor (carrera, tutor) VALUES (?, ?)");
            $stmtInsert->bind_param("ii", $carrera, $idTutor);
            $stmtInsert->execute();
            $idCarreraTutor = $stmtInsert->insert_id;
            $stmtInsert->close();
        }
        $stmt->close();
        return $idCarreraTutor;
    }

    public function createReporteTutoria($data)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO reporte_tutoria (carreraTutor, periodo, numTutoria, fechaInicioTutoria, fechaFinTutoria, numAsistencia, numRiesgo, comentario, fechaCreacion, esBorrador)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            'iiisssissi',
            $data['carreraTutor'],
            $data['periodo'],
            $data['numTutoria'],
            $data['fechaInicio'],
            $data['fechaFin'],
            $data['numAsistencias'],
            $data['numRiesgo'],
            $data['comentario'],
            $data['fechaCreacion'],
            $data['esBorrador']
        );
        $stmt->execute();
        $idReporte = $stmt->insert_id;
        $stmt->close();
        return $idReporte;
    }

    public function insertProblematicasAcademicas($problematicasData)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO problematica_academica (experienciaEducativa, profesor, problematica, otro, numAlumnos, estado, reporte)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        foreach ($problematicasData as $data) {
            $stmt->bind_param(
                'iissisi',
                $data['experiencia'],
                $data['profesor'],
                $data['problematica'],
                $data['otro'],
                $data['numAlumnos'],
                $data['estado'],
                $data['reporte']
            );
            $stmt->execute();
        }
        $stmt->close();
    }

    public function getTutorIdByCorreo($correoInstitucional)
    {
        $stmt = $this->conn->prepare("SELECT idTutor FROM tutor WHERE correoInstitucional = ?");
        $stmt->bind_param("s", $correoInstitucional);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $idTutor = $row['idTutor'];
        } else {
            $idTutor = null;
        }
        $stmt->close();
        return $idTutor;
    }

    public function getReporteById($idReporte)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                rt.idReporte,
                rt.periodo,
                tc.carrera,
                tc.tutor,
                rt.fechaInicioTutoria,
                rt.fechaFinTutoria,
                rt.numTutoria,
                rt.numAsistencia,
                rt.numRiesgo,
                rt.comentario,
            CONCAT(t.nombre, ' ', COALESCE(t.apellidoPaterno, ''), ' ', COALESCE(t.apellidoMaterno, '')) AS nombreTutor,
            c.nombre AS nombreCarrera,
            p.nombre AS nombrePeriodo
            FROM reporte_tutoria rt
            INNER JOIN carrera_tutor tc ON tc.idCarreraTutor = rt.carreraTutor
            INNER JOIN tutor t ON t.idTutor = tc.tutor
            INNER JOIN carrera c ON c.idCarrera = tc.carrera
            INNER JOIN periodo p ON p.idPeriodo = rt.periodo
            WHERE rt.idReporte = ?
        ");
        $stmt->bind_param("i", $idReporte);
        $stmt->execute();
        $result = $stmt->get_result();
        $reporte = $result->fetch_assoc();
        $stmt->close();
        return $reporte;
    }

    public function updateReporteTutoria($idReporte, $data)
    {
        $stmt = $this->conn->prepare("
            UPDATE reporte_tutoria rt
            INNER JOIN carrera_tutor ct ON rt.carreraTutor = ct.idCarreraTutor
            SET
                ct.carrera = ?,
                rt.periodo = ?,
                rt.numTutoria = ?,
                rt.fechaInicioTutoria = ?,
                rt.fechaFinTutoria = ?,
                rt.numAsistencia = ?,
                rt.numRiesgo = ?,
                rt.comentario = ?,
                rt.esBorrador = ?
            WHERE rt.idReporte = ?
        ");

        $stmt->bind_param(
            "iiisssisii",
            $data['carrera'],
            $data['periodo'],
            $data['numTutoria'],
            $data['fechaInicioTutoria'],
            $data['fechaFinTutoria'],
            $data['numAsistencia'],
            $data['numRiesgo'],
            $data['comentario'],
            $data['esBorrador'],
            $idReporte
        );
        $stmt->execute();
        $stmt->close();
    }

    public function getTutorByReporteId($idReporte)
    {
        $stmt = $this->conn->prepare("
        SELECT t.correoInstitucional
        FROM reporte_tutoria rt
        INNER JOIN carrera_tutor ct ON rt.carreraTutor = ct.idCarreraTutor
        INNER JOIN tutor t ON ct.tutor = t.idTutor
        WHERE rt.idReporte = ?
    ");
        $stmt->bind_param("i", $idReporte);
        $stmt->execute();
        $result = $stmt->get_result();
        $tutorData = $result->fetch_assoc();
        $stmt->close();

        return $tutorData;
    }

    public function deleteReporteTutoria($idReporte)
    {
        $stmt = $this->conn->prepare("DELETE FROM reporte_tutoria WHERE idReporte = ?");
        $stmt->bind_param("i", $idReporte);
        $stmt->execute();
        $stmt->close();
    }

    public function deleteCarreraTutorIfUnused($idCarreraTutor)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM reporte_tutoria WHERE carreraTutor = ?");
        $stmt->bind_param("i", $idCarreraTutor);
        $stmt->execute();
        $result = $stmt->get_result();
        $countData = $result->fetch_assoc();
        $stmt->close();

        if ($countData['count'] == 0) {
            $stmt = $this->conn->prepare("DELETE FROM carrera_tutor WHERE idCarreraTutor = ?");
            $stmt->bind_param("i", $idCarreraTutor);
            $stmt->execute();
            $stmt->close();
        }
    }
}
?>