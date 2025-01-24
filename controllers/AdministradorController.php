<?php
require_once '../config/config.php';
require_once '../config/connection.php';
require_once '../models/Administrador.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


class AdministradorController
{
    private $conn;
    private $administradorModel;

    public function __construct()
    {
        $this->conn = connectiondb();
        $this->administradorModel = new Administrador($this->conn);
    }

    public function showAdministradores()
    {
        session_start();

        $rolesPermitidos = [3];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
            unset($_SESSION['message']);
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $user = $_SESSION['user'];
        $csrf_token = $_SESSION['csrf_token'];
        $correoAdministrador = $_SESSION['correoInstitucional'];

        $stmt = $this->conn->prepare("
            SELECT a.idAdministrador 
            FROM administrador a 
            WHERE a.correoInstitucional = ?
        ");
        $stmt->bind_param("s", $correoAdministrador);
        $stmt->execute();
        $stmt->bind_result($idAdministrador);
        $stmt->fetch();
        $stmt->close();

        if (!$idAdministrador) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        $administradores = $this->administradorModel->getAdministradores($idAdministrador);

        require_once '../views/administrarAdministradores.php';
    }

    public function showCreateForm()
    {
        session_start();

        $rolesPermitidos = [3];
        if (!isset($_SESSION['user']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $user = $_SESSION['user'];
        $csrf_token = $_SESSION['csrf_token'];
        $rol = 3;

        if (isset($_SESSION['errors'])) {
            $errors = $_SESSION['errors'];
            unset($_SESSION['errors']);
        }

        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
            unset($_SESSION['message']);
        }

        require_once '../views/registroAdministrador.php';
    }

    public function createAdministrador()
    {
        session_start();

        $rolesPermitidos = [3];
        if (!isset($_SESSION['user']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                echo "<p style='color: red;'>Error: Solicitud no válida.</p>";
                exit();
            }

            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $apellidoPaterno = isset($_POST['paterno']) ? trim($_POST['paterno']) : '';
            $apellidoMaterno = isset($_POST['materno']) ? trim($_POST['materno']) : '';
            $correoInstitucional = isset($_POST['correoInstitucional']) ? trim($_POST['correoInstitucional']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
            $rol = 3;

            $errors = [];

            if (empty($nombre)) {
                $errors[] = 'El campo "Nombre" es obligatorio.';
            }
            if (empty($correoInstitucional)) {
                $errors[] = 'El campo "Correo institucional" es obligatorio.';
            }
            if (empty($password)) {
                $errors[] = 'El campo "Contraseña" es obligatorio.';
            }
            if (empty($confirmPassword)) {
                $errors[] = 'El campo "Confirmar Contraseña" es obligatorio.';
            }
            if ($password !== $confirmPassword) {
                $errors[] = 'Las contraseñas no coinciden.';
            }

            if (!empty($errors)) {
                echo "<div style='color: red;'>";
                foreach ($errors as $error) {
                    echo "<p>" . htmlspecialchars($error) . "</p>";
                }
                echo "</div>";
                return;
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $data = [
                'nombre' => $nombre,
                'apellidoPaterno' => $apellidoPaterno,
                'apellidoMaterno' => $apellidoMaterno,
                'correoInstitucional' => $correoInstitucional,
                'rol' => $rol,
                'password' => $hashedPassword
            ];


            try {
                $resultado = $this->administradorModel->createAdministrador($data);

                if ($resultado) {
                    $_SESSION['message'] = "Administrador registrado exitosamente.";
                    header("Location: " . BASE_URL . "/administrarAdministradores.php");
                    exit();
                } else {
                    $_SESSION['message'] = "Error al registrar el administrador.";
                    header("Location: " . BASE_URL . "/registroAdministrador.php");
                    exit();
                }

            } catch (Exception $e) {
                echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        } else {
            echo "<p style='color: red;'>Error: Método no permitido.</p>";
        }
    }



    public function showEditForm()
    {
        session_start();

        $rolesPermitidos = [3];
        if (!isset($_SESSION['user']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idAdministrador = isset($_POST['idAdministrador']) ? intval($_POST['idAdministrador']) : 0;

            if ($idAdministrador > 0) {
                $administrador = $this->administradorModel->getAdministradorById($idAdministrador);

                if ($administrador) {
                    if (empty($_SESSION['csrf_token'])) {
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    }

                    $user = $_SESSION['user'];
                    $csrf_token = $_SESSION['csrf_token'];

                    if (isset($_SESSION['errors'])) {
                        $errors = $_SESSION['errors'];
                        unset($_SESSION['errors']);
                    }

                    if (isset($_SESSION['message'])) {
                        $message = $_SESSION['message'];
                        unset($_SESSION['message']);
                    }

                    require_once '../views/editarAdministrador.php';
                } else {
                    $_SESSION['message'] = 'Administrador no encontrado';
                    header('Location: ' . BASE_URL . '/administrarAdministradores.php');
                    exit();
                }
            } else {
                $_SESSION['message'] = 'ID de administrador inválido';
                header('Location: ' . BASE_URL . '/administrarAdministradores.php');
                exit();
            }
        } else {
            header('Location: ' . BASE_URL . '/administrarAdministradores.php');
            exit();
        }
    }

    public function updateAdministrador()
    {
        session_start();

        $rolesPermitidos = [3];
        if (!isset($_SESSION['user']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die('Error: solicitud inválida o CSRF token no válido.');
            }

            $idAdministrador = isset($_POST['idAdministrador']) ? intval($_POST['idAdministrador']) : 0;

            if ($idAdministrador <= 0) {
                $_SESSION['message'] = 'ID de administrador inválido';
                header('Location: ' . BASE_URL . '/administrarAdministradores.php');
                exit();
            }

            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $apellidoPaterno = isset($_POST['paterno']) ? trim($_POST['paterno']) : '';
            $apellidoMaterno = isset($_POST['materno']) ? trim($_POST['materno']) : '';
            $correoInstitucional = isset($_POST['correoInstitucional']) ? trim($_POST['correoInstitucional']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

            $errors = [];

            if (empty($nombre)) {
                $errors[] = 'El campo "Nombre" es obligatorio.';
            }
            if (empty($correoInstitucional)) {
                $errors[] = 'El campo "Correo institucional" es obligatorio.';
            }

            // Validar la contraseña si se proporciona
            if ($password || $confirmPassword) {
                if ($password !== $confirmPassword) {
                    $errors[] = 'Las nuevas contraseñas no coinciden.';
                }
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $_POST; // Para mantener los datos ingresados
                header('Location: ' . BASE_URL . '/editarAdministrador.php');
                exit();
            }

            $data = [
                'nombre' => $nombre,
                'apellidoPaterno' => $apellidoPaterno,
                'apellidoMaterno' => $apellidoMaterno,
                'correoInstitucional' => $correoInstitucional
            ];

            if ($password) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $data['password'] = $hashedPassword;
            }

            $resultado = $this->administradorModel->updateAdministrador($idAdministrador, $data);

            if ($resultado) {
                $_SESSION['message'] = "Administrador actualizado exitosamente.";
                header('Location: ' . BASE_URL . '/administrarAdministradores.php');
                exit();
            } else {
                $_SESSION['message'] = "Error al actualizar el administrador.";
                header("Location: " . BASE_URL . "/editarAdministrador.php");
                exit();
            }
        } else {
            header('Location: ' . BASE_URL . '/administrarAdministradores.php');
            exit();
        }
    }

    public function deleteAdministrador()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                echo json_encode(['status' => 'error', 'message' => 'Token CSRF inválido.']);
                exit();
            }

            $rolesPermitidos = [3];
            if (!isset($_SESSION['user']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
                echo json_encode(['status' => 'error', 'message' => 'Acceso denegado.']);
                exit();
            }

            $idAdministrador = isset($_POST['idAdministrador']) ? intval($_POST['idAdministrador']) : 0;

            if ($idAdministrador > 0) {
                $resultado = $this->administradorModel->deleteAdministrador($idAdministrador);

                if ($resultado) {
                    echo json_encode(['status' => 'success', 'message' => 'Administrador eliminado con éxito.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el administrador.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ID de administrador inválido.']);
            }
        } else {
            header('Location: ' . BASE_URL . '/administrarAdministradores.php');
            exit();
        }
    }
}