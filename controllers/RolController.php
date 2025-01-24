<?php
require_once '../config/config.php';
require_once '../config/connection.php';
require_once '../models/Profesor.php';
require_once '../models/Coordinador.php';

class RolController
{
    private $conn;
    private $tutorModel;
    private $coordinadorModel;

    public function __construct()
    {
        $this->conn = connectiondb();
        $this->tutorModel = new Profesor($this->conn);
        $this->coordinadorModel = new Coordinador($this->conn);
    }

    public function showUpdateRoleForm()
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

        $tutores = $this->tutorModel->getTutorsWithSession();

        require_once '../models/Carrera.php';
        $carreraModel = new Carrera($this->conn);
        $carreras = $carreraModel->getCarreras();

        require_once '../views/actualizarRol.php';
    }

    public function updateRole()
    {
        session_start();

        $rolesPermitidos = [3];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die('Error: solicitud inválida o CSRF token no válido.');
            }

            $sesionId = isset($_POST['tutor']) ? intval($_POST['tutor']) : 0;
            $nuevoRol = isset($_POST['rol']) ? intval($_POST['rol']) : 0;
            $carrerasSeleccionadas = isset($_POST['carreras']) ? $_POST['carreras'] : [];

            $errors = [];

            if ($sesionId <= 0) {
                $errors[] = 'Debe seleccionar un tutor válido.';
            }

            if (!in_array($nuevoRol, [1, 3, 4, 5])) {
                $errors[] = 'Debe seleccionar un rol válido.';
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header('Location: ' . BASE_URL . '/actualizarRol.php');
                exit();
            }

            $profesorData = $this->tutorModel->getTutorBySesionId($sesionId);
            if (!$profesorData) {
                $_SESSION['errors'] = ['No se encontró el profesor seleccionado.'];
                header('Location: ' . BASE_URL . '/actualizarRol.php');
                exit();
            }

            $correoInstitucional = $profesorData['correoInstitucional'];
            $idTutor = $profesorData['idTutor'];

            $this->conn->begin_transaction();
            try {
                require_once '../models/Coordinador.php';
                $coordinadorModel = new Coordinador($this->conn);

                $coordinadorModel->deleteCarrerasBySesion($sesionId);
                
                $resultado = $this->tutorModel->updateTutorRole($sesionId, $nuevoRol);


                if (!$resultado) {
                    throw new Exception("Error al actualizar el rol del tutor.");
                }

                if ($nuevoRol == 4) {

                    $data = [
                        'idTutor' => $idTutor,
                        'correoInstitucional' => $correoInstitucional,
                        'sesionId' => $sesionId,
                        'carreras' => $carrerasSeleccionadas
                    ];
                    $resultadoCoordinador = $coordinadorModel->assignCarrerasToCoordinador($data);

                    if (!$resultadoCoordinador) {
                        throw new Exception("Error al asignar las carreras al coordinador.");
                    }
                } else {
                    require_once '../models/Coordinador.php';
                    $coordinadorModel = new Coordinador($this->conn);
                    $coordinadorModel->deleteCarrerasBySesion($sesionId);
                }

                $this->conn->commit();
                $_SESSION['message'] = "Rol actualizado exitosamente.";

            } catch (Exception $e) {
                $this->conn->rollback();
                $_SESSION['errors'] = ["Error al actualizar el rol: " . $e->getMessage()];
            }

            header('Location: ' . BASE_URL . '/actualizarRol.php');
            exit();
        } else {
            header('Location: ' . BASE_URL . '/actualizarRol.php');
            exit();
        }
    }
}