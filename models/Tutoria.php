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
        $stmt = $this->conn->prepare("
            SELECT tt.idTutoria, 
                   CONCAT(t.nombre, ' ', COALESCE(t.apellidoPaterno, ''), ' ', COALESCE(t.apellidoMaterno, '')) AS tutorNombre, 
                   c.nombre AS carrera, 
                   tt.numTutoria AS tutoria, 
                   tt.fecha, 
                   CONCAT(COALESCE(TIME_FORMAT(tt.horaInicio, '%H:%i'), ''), ' - ', COALESCE(TIME_FORMAT(tt.horaFin, '%H:%i'), '')) AS horario, 
                   tt.lugar, 
                   tt.nota, 
                   tt.archivo,
                   p.nombre as periodo
            FROM tutor t
            INNER JOIN tutoria tt ON tt.tutor = t.idTutor
            INNER JOIN periodo p ON p.idPeriodo = tt.periodo
            INNER JOIN carrera c ON c.idCarrera = tt.carrera
            WHERE t.correoInstitucional = ?
        ");
        $stmt->bind_param("s", $correoInstitucional);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    public function getCarrerasByTutor($idTutor)
    {
        $stmt = $this->conn->prepare("
            SELECT DISTINCT c.idCarrera, c.nombre
            FROM carrera c
            INNER JOIN tutorado tdo ON tdo.carrera = c.idCarrera
            WHERE tdo.tutor = ?
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

        $stmt = $this->conn->prepare("
            INSERT INTO tutoria (tutor, carrera, periodo, numTutoria, modalidad, periodoAtencion, lugar, fecha, horaInicio, horaFin, nota, archivo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "iiisssssssss",
            $idTutor,
            $data['carrera'],
            $data['periodo'],
            $data['numTutoria'],
            $data['modalidad'],
            $data['periodoAtencion'],
            $data['lugar'],
            $data['fecha'],
            $data['hora_inicio'],
            $data['hora_final'],
            $data['notas'],
            $archivoNombre
        );

        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();

        return $affectedRows > 0;
    }

    public function getTutoriaById($idTutoria)
    {
        $stmt = $this->conn->prepare("
        SELECT t.numTutoria, t.modalidad, t.periodoAtencion, t.lugar, t.fecha, t.horaInicio, t.horaFin, t.nota, t.carrera, t.periodo, t.archivo
        FROM tutoria t
        WHERE t.idTutoria = ?
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
        $sql = "UPDATE tutoria SET carrera = ?, periodo = ?, numTutoria = ?, modalidad = ?, periodoAtencion = ?, lugar = ?, fecha = ?, horaInicio = ?, horaFin = ?, nota = ?";
        $params = [];
        $types = 'iiisssssss'; // 10 caracteres
    
        $carrera = $data['carrera'];
        $periodo = $data['periodo'];
        $numTutoria = $data['numTutoria'];
        $modalidad = $data['modalidad'];
        $periodoAtencion = $data['periodoAtencion'];
        $lugar = !empty($data['lugar']) ? $data['lugar'] : null;
        $fecha = !empty($data['fecha']) ? $data['fecha'] : null;
        $horaInicio = !empty($data['hora_inicio']) ? $data['hora_inicio'] : null;
        $horaFin = !empty($data['hora_final']) ? $data['hora_final'] : null;
        $notas = !empty($data['notas']) ? $data['notas'] : null;
    
        $params = [
            &$carrera,          // 'i'
            &$periodo,          // 'i'
            &$numTutoria,       // 'i'
            &$modalidad,        // 's'
            &$periodoAtencion,  // 's'
            &$lugar,            // 's'
            &$fecha,            // 's'
            &$horaInicio,       // 's'
            &$horaFin,          // 's'
            &$notas             // 's'
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

        $stmt = $this->conn->prepare("
        SELECT tt.correoInstitucional
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
        $stmt = $this->conn->prepare("
           SELECT 
                ttt.idTutoria, 
                CONCAT(tt.nombre, ' ', COALESCE(tt.apellidoPaterno, ''), ' ', COALESCE(tt.apellidoMaterno, '')) AS tutorNombre, 
                c.nombre AS carrera, 
                ttt.numTutoria AS tutoria, 
                ttt.fecha, 
                CONCAT(COALESCE(TIME_FORMAT(ttt.horaInicio, '%H:%i'), ''), ' - ', COALESCE(TIME_FORMAT(ttt.horaFin, '%H:%i'), '')) AS horario, 
                ttt.lugar, 
                ttt.nota, 
                ttt.archivo
            FROM 
                tutoria ttt
            INNER JOIN 
                tutor tt ON tt.idTutor = ttt.tutor
            INNER JOIN 
                carrera c ON c.idCarrera = ttt.carrera
            INNER JOIN 
                tutorado tu ON tu.tutor = tt.idTutor
            WHERE tu.correoInstitucional = ? AND tu.carrera = ttt.carrera
            ORDER BY 
                ttt.numTutoria DESC;
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
        $stmt = $this->conn->prepare("
            SELECT tt.idTutoria, 
                CONCAT(t.nombre, ' ', COALESCE(t.apellidoPaterno, ''), ' ', COALESCE(t.apellidoMaterno, '')) AS tutorNombre, 
                c.nombre AS carrera, 
                tt.numTutoria AS tutoria, 
                tt.fecha, 
                CONCAT(COALESCE(TIME_FORMAT(tt.horaInicio, '%H:%i'), ''), ' - ', COALESCE(TIME_FORMAT(tt.horaFin, '%H:%i'), '')) AS horario, 
                tt.lugar, 
                tt.nota, 
                tt.archivo
            FROM tutor t
            INNER JOIN tutoria tt ON tt.tutor = t.idTutor
            INNER JOIN periodo p ON p.idPeriodo = tt.periodo
            INNER JOIN carrera c ON c.idCarrera = tt.carrera
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
        $stmt = $this->conn->prepare("
            SELECT tt.idTutoria, 
                CONCAT(t.nombre, ' ', COALESCE(t.apellidoPaterno, ''), ' ', COALESCE(t.apellidoMaterno, '')) AS tutorNombre, 
                c.nombre AS carrera, 
                tt.numTutoria AS tutoria, 
                tt.fecha, 
                CONCAT(COALESCE(TIME_FORMAT(tt.horaInicio, '%H:%i'), ''), ' - ', COALESCE(TIME_FORMAT(tt.horaFin, '%H:%i'), '')) AS horario, 
                tt.lugar, 
                tt.nota, 
                tt.archivo
            FROM tutoria tt
            INNER JOIN tutor t ON t.idTutor = tt.tutor
            INNER JOIN carrera c ON c.idCarrera = tt.carrera
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