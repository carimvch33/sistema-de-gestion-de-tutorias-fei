<?php

class User
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function findUser($user)
    {
        $user = $this->conn->real_escape_string($user);

        if (strpos($user, '@') === false) {
            $correo_uv = $user . '@uv.mx';
            $correo_estudiante = $user . '@estudiantes.uv.mx';
        } else {
            $correo_uv = $user;
            $correo_estudiante = $user;
        }

        $stmt = $this->conn->prepare("SELECT s.idSesion, s.correoInstitucional, s.rol
                                      FROM sesion s
                                      WHERE s.correoInstitucional = ? OR s.correoInstitucional = ?");
        $stmt->bind_param("ss", $correo_uv, $correo_estudiante);
        $stmt->execute();
        $loginResult = $stmt->get_result();
        $stmt->close();

        if ($loginResult->num_rows > 0) {
            $userRow = $loginResult->fetch_assoc();
            $rol = $userRow['rol'];
            $correo = $userRow['correoInstitucional'];

            if ($rol == 1) {
                // Tutor
                $stmt = $this->conn->prepare("SELECT t.nombre, t.correoInstitucional, s.rol
                                              FROM sesion s
                                              INNER JOIN tutor t ON t.correoInstitucional = s.correoInstitucional
                                              WHERE s.correoInstitucional = ?");
            } elseif ($rol == 2) {
                // Tutorado
                $stmt = $this->conn->prepare("SELECT t.nombre, t.correoInstitucional, s.rol
                                              FROM sesion s
                                              INNER JOIN tutorado t ON t.correoInstitucional = s.correoInstitucional
                                              WHERE s.correoInstitucional = ?");
            } elseif ($rol == 3) {
                // Administrador
                $stmt = $this->conn->prepare("SELECT a.nombre, a.correoInstitucional, s.rol
                                              FROM sesion s
                                              INNER JOIN administrador a ON a.correoInstitucional = s.correoInstitucional
                                              WHERE s.correoInstitucional = ?");
            } elseif ($rol == 4) {
                // Coordinador
                $stmt = $this->conn->prepare("SELECT t.nombre, t.correoInstitucional, s.rol
                                              FROM sesion s
                                              INNER JOIN tutor t ON t.correoInstitucional = s.correoInstitucional
                                              WHERE s.correoInstitucional = ?");
            } else {
                return null;
            }

            $stmt->bind_param("s", $correo);
            $stmt->execute();
            $result = $stmt->get_result();
            $userInfo = $result->fetch_assoc();
            $stmt->close();

            $periodoConsulta = $this->conn->prepare("SELECT p.nombre 
                                                     FROM periodo p 
                                                     WHERE p.actual = 1 
                                                     LIMIT 1");
            $periodoActual = null;

            if ($periodoConsulta->execute()) {
                $result = $periodoConsulta->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $periodoActual = $row['nombre'];
                } else {
                    $periodoActual = 'Período no definido';
                }
            } else {
                $periodoActual = 'Error al obtener el período';
            }
            $periodoConsulta->close();

            $userInfo['periodoActual'] = $periodoActual;

            return $userInfo;
        } else {
            return null;
        }
    }
}
?>