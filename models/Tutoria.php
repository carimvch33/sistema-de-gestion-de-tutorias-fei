<?php
class Tutoria
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getTutoriasByTutor($correoInstitucional)
    {
        $stmt = $this->conn->prepare("SELECT 
                tt.idTutoria, 
                CONCAT(t.nombre, ' ', COALESCE(t.apellidoPaterno, ''), ' ', COALESCE(t.apellidoMaterno, '')) AS tutorNombre, 
                c.nombre AS carrera, 
                pt.numSesion AS tutoria, 
                tt.fechaInicio,
                tt.fechaFin, 
                tt.lugar, 
                tt.nota, 
                tt.archivo,
                p.nombre AS periodo
            FROM tutor t
            INNER JOIN tutoria tt ON tt.tutor = t.idTutor
            INNER JOIN periodo_tutorias pt ON pt.idPeriodoTutorias = tt.periodoTutorias
            INNER JOIN carrera c ON c.idCarrera = pt.carrera
            INNER JOIN periodo p ON p.idPeriodo = pt.periodo
            WHERE t.correoInstitucional = ? 
            AND p.actual = 1;
        ");
        $stmt->bind_param("s", $correoInstitucional);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    public function getTutoringHistoryByTutor($institutionalMail)
    {
        $stmt = $this->conn->prepare("SELECT tt.idTutoria, 
                   CONCAT(t.nombre, ' ', COALESCE(t.apellidoPaterno, ''), ' ', COALESCE(t.apellidoMaterno, '')) AS tutorNombre, 
                   c.nombre AS carrera, 
                   pt.numSesion AS tutoria, 
                   tt.fechaInicio,
                   tt.fechaFin, 
                   tt.lugar, 
                   tt.nota, 
                   tt.archivo,
                   p.nombre as periodo
            FROM tutor t
            INNER JOIN tutoria tt ON tt.tutor = t.idTutor
            INNER JOIN periodo_tutorias pt ON pt.idPeriodoTutorias = tt.periodoTutorias
            INNER JOIN carrera c ON c.idCarrera = pt.carrera
            INNER JOIN periodo p ON p.idPeriodo = pt.periodo
            WHERE t.correoInstitucional = ? AND p.actual = 0
        ");
        $stmt->bind_param("s", $institutionalMail);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    public function getCarrerasByTutor($idTutor)
    {
        $stmt = $this->conn->prepare("SELECT DISTINCT c.idCarrera, c.nombre
            FROM carrera c
            INNER JOIN carrera_tutor ct ON ct.carrera = c.idCarrera
            WHERE ct.tutor = ?
        ");

        $stmt->bind_param("i", $idTutor);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    public function crearTutoria($data, $correoInstitucional, $archivoNombre = null)
    {
        $stmt = $this->conn->prepare("SELECT idTutor FROM tutor WHERE correoInstitucional = ?");
        $stmt->bind_param("s", $correoInstitucional);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows === 0) {
            return false;
        }

        $row = $result->fetch_assoc();
        $idTutor = $row['idTutor'];

        $stmt = $this->conn->prepare("INSERT INTO tutoria (modalidad, fechaInicio, fechaFin, lugar, nota, archivo, tutor, periodoTutorias)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?);
        ");

        $stmt->bind_param(
            "ssssssii",
            $data['modalidad'],
            $data['fecha'],
            $data['fecha_fin'],
            $data['lugar'],
            $data['notas'],
            $archivoNombre,
            $idTutor,
            $data['periodoTutoria']
        );

        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();

        return $affectedRows > 0;
    }

    public function getTutoriaById($idTutoria)
    {
        $stmt = $this->conn->prepare("SELECT 
            t.modalidad,
            t.fechaInicio,
            t.fechaFin,
            t.lugar,
            t.nota,
            t.archivo,
            t.periodoTutorias
        FROM tutoria t
        WHERE t.idTutoria = ?;
    ");
        $stmt->bind_param("i", $idTutoria);
        $stmt->execute();
        $result = $stmt->get_result();
        $tutoria = $result->fetch_assoc();
        $stmt->close();

        return $tutoria;
    }

    public function updateTutoria($idTutoria, $idTutor, $data, $archivoNombre = null)
    {
        $sql = "UPDATE tutoria SET modalidad = ?, lugar = ?, fechaInicio = ?, fechaFin = ?, nota = ?, periodoTutorias = ?";
        $params = [];
        $types = 'sssssi';
        
        $modalidad = $data['modalidad'];
        $lugar = isset($data['lugar']) ? $data['lugar'] : null;
        $fechaInicio = isset($data['fecha']) ? $data['fecha'] : null;
        $fechaFin = isset($data['fecha_fin']) ? $data['fecha_fin'] : null;
        $notas = isset($data['notas']) ? $data['notas'] : null;
        $periodoTutorias = $data['periodoTutoria'];

        $params = [
            &$modalidad,        // 's'
            &$lugar,            // 's'
            &$fechaInicio,      // 's'
            &$fechaFin,         // 's'
            &$notas,            // 's'
            &$periodoTutorias   // 'i'
        ];

        if ($archivoNombre !== null) {
            $sql .= ", archivo = ?";
            $types .= 's';
            $params[] = &$archivoNombre;
        }

        $sql .= " WHERE idTutoria = ? AND tutor = ?";
        $types .= 'ii';
        $params[] = &$idTutoria;
        $params[] = &$idTutor;

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $this->conn->error);
        }

        if (strlen($types) !== count($params)) {
            die("Número de tipos y variables no coincide");
        }

        $stmt->bind_param($types, ...$params);

        $stmt->execute();

        $affectedRows = $stmt->affected_rows;
        $stmt->close();

        return $affectedRows > 0;
    } 

    public function getCorreoCreadorTutoria($idTutoria)
    {
        $creadorCorreo = null;

        $stmt = $this->conn->prepare("SELECT tt.correoInstitucional
        FROM tutoria t
        INNER JOIN tutor tt ON tt.idTutor = t.tutor
        WHERE t.idTutoria = ?
    ");
        $stmt->bind_param("i", $idTutoria);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $creadorCorreo = $row['correoInstitucional'];
        }

        $stmt->close();

        return $creadorCorreo;
    }

    public function handleUploadedFile($file, $idTutoria)
    {
        $archivoExistente = $this->getArchivoTutoria($idTutoria);
        $archivoNombre = $archivoExistente;

        if (isset($file) && $file['error'] == UPLOAD_ERR_OK) {
            $archivoNombreNuevo = basename($file['name']);
            $archivoTmp = $file['tmp_name'];
            $archivoDestino = './uploads/' . $archivoNombreNuevo;

            if (move_uploaded_file($archivoTmp, $archivoDestino)) {
                $archivoNombre = $archivoNombreNuevo;

                if ($archivoExistente && $archivoExistente !== $archivoNombreNuevo && file_exists('./uploads/' . $archivoExistente)) {
                    unlink('./uploads/' . $archivoExistente);
                }
            } else {
                $_SESSION['message'] = 'Error al subir el archivo.';
                header('Location: ./editarTutoria.php?idTutoria=' . $idTutoria);
                exit();
            }
        }

        return $archivoNombre;
    }

    public function getArchivoTutoria($idTutoria)
    {
        $archivo = null;

        $stmt = $this->conn->prepare("SELECT archivo FROM tutoria WHERE idTutoria = ?");
        $stmt->bind_param("i", $idTutoria);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $archivo = $row['archivo'];
        }

        $stmt->close();

        return $archivo;
    }

    public function eliminarTutoria($idTutoria, $idTutor)
    {
        $stmt = $this->conn->prepare("SELECT idTutoria FROM tutoria WHERE idTutoria = ? AND tutor = ?");
        $stmt->bind_param("ii", $idTutoria, $idTutor);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows === 0) {
            return false;
        }

        $archivo = $this->getArchivoTutoria($idTutoria);
        if ($archivo) {
            $uploadDir = dirname(__DIR__) . '/uploads/';
            $archivoPath = $uploadDir . $archivo;
            if (file_exists($archivoPath)) {
                unlink($archivoPath);
            }
        }

        $stmt = $this->conn->prepare("DELETE FROM tutoria WHERE idTutoria = ? AND tutor = ?");
        $stmt->bind_param("ii", $idTutoria, $idTutor);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();

        return $affectedRows > 0;
    }

    public function getSesionesByTutorado($correoTutorado)
    {
        $stmt = $this->conn->prepare("SELECT 
                ttt.idTutoria, 
                CONCAT(tt.nombre, ' ', COALESCE(tt.apellidoPaterno, ''), ' ', COALESCE(tt.apellidoMaterno, '')) AS tutorNombre, 
                c.nombre AS carrera, 
                pt.numSesion AS tutoria, 
                ttt.fechaInicio,
                ttt.fechaFin, 
                ttt.lugar, 
                ttt.nota, 
                ttt.archivo,
                p.nombre as periodo
            FROM 
                tutoria ttt
            INNER JOIN 
                tutor tt ON tt.idTutor = ttt.tutor
            INNER JOIN 
                periodo_tutorias pt ON pt.idPeriodoTutorias = ttt.periodoTutorias
            INNER JOIN 
                carrera c ON c.idCarrera = pt.carrera
            INNER JOIN 
                tutorado tu ON tu.tutor = tt.idTutor
            INNER JOIN
                periodo p ON p.idPeriodo = pt.periodo
            WHERE tu.correoInstitucional = ? AND tu.carrera = pt.carrera AND p.actual = 1
            ORDER BY 
                pt.numSesion DESC
        ");

        $stmt->bind_param("s", $correoTutorado);
        $stmt->execute();
        $result = $stmt->get_result();

        $sesiones = [];
        while ($row = $result->fetch_assoc()) {
            $sesiones[] = $row;
        }

        $stmt->close();

        return $sesiones;
    }

    public function getAllTutorias()
    {
        $stmt = $this->conn->prepare("SELECT tt.idTutoria, 
                CONCAT(t.nombre, ' ', COALESCE(t.apellidoPaterno, ''), ' ', COALESCE(t.apellidoMaterno, '')) AS tutorNombre, 
                c.nombre AS carrera, 
                pt.numSesion AS tutoria, 
                tt.fechaInicio,
                tt.fechaFin, 
                tt.lugar, 
                tt.nota, 
                tt.archivo,
                p.nombre as periodo
            FROM tutor t
            INNER JOIN tutoria tt ON tt.tutor = t.idTutor
            INNER JOIN periodo_tutorias pt ON pt.idPeriodoTutorias = tt.periodoTutorias
            INNER JOIN carrera c ON c.idCarrera = pt.carrera
            INNER JOIN periodo p ON p.idPeriodo = pt.periodo
        ");
        $stmt->execute();
        $result = $stmt->get_result();

        $tutorias = [];
        while ($row = $result->fetch_assoc()) {
            $tutorias[] = $row;
        }

        $stmt->close();

        return $tutorias;
    }

    public function getTutoriasByCoordinador($idSesion)
    {
        $stmt = $this->conn->prepare("SELECT tt.idTutoria, 
                CONCAT(t.nombre, ' ', COALESCE(t.apellidoPaterno, ''), ' ', COALESCE(t.apellidoMaterno, '')) AS tutorNombre, 
                c.nombre AS carrera, 
                pt.numSesion AS tutoria, 
                tt.fechaInicio,
                tt.fechaFin, 
                tt.lugar, 
                tt.nota, 
                tt.archivo,
                p.nombre as periodo
            FROM tutoria tt
            INNER JOIN tutor t ON t.idTutor = tt.tutor
            INNER JOIN periodo_tutorias pt ON pt.idPeriodoTutorias = tt.periodoTutorias
            INNER JOIN carrera c ON c.idCarrera = pt.carrera
            INNER JOIN periodo p ON p.idPeriodo = pt.periodo
            INNER JOIN coordinador_carrera cc ON cc.idCarrera = c.idCarrera
            WHERE cc.idSesion = ?
        ");
        $stmt->bind_param("i", $idSesion);
        $stmt->execute();
        $result = $stmt->get_result();
        $tutorias = [];
        while ($row = $result->fetch_assoc()) {
            $tutorias[] = $row;
        }
        $stmt->close();
        return $tutorias;
    }

}
?>