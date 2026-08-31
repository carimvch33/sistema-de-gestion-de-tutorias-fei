<?php
require_once '../config/config.php';
require_once '../config/connection.php';
require_once '../models/PeriodoEscolar.php';

class PeriodoEscolarController
{
    private $conn;
    private $periodoModel;

    public function __construct()
    {
        $this->conn = connectiondb();
        $this->periodoModel = new PeriodoEscolar($this->conn);
    }

    public function showPeriodos()
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

        $periodos = $this->periodoModel->getPeriodos();

        require_once '../views/administrarPeriodosEscolares.php';
    }

    public function showCreateForm()
    {
        session_start();

        $rolesPermitidos = [3];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

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

        require_once '../views/registroPeriodo.php';
    }

    public function createPeriodo()
    {
        session_start();

        $rolesPermitidos = [3];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $_SESSION['message'] = "Error: Solicitud no válida.";
                header('Location: ' . BASE_URL . '/registroPeriodo.php');
                exit();
            }

            $nombrePeriodo = isset($_POST['periodo']) ? trim($_POST['periodo']) : null;
            $actual = isset($_POST['actual']) ? intval($_POST['actual']) : null;

            $fechas = [
                1 => [
                    'inicio' => isset($_POST['fechaInicio1']) ? $_POST['fechaInicio1'] : null,
                    'fin' => isset($_POST['fechaFin1']) ? $_POST['fechaFin1'] : null,
                ],
                2 => [
                    'inicio' => isset($_POST['fechaInicio2']) ? $_POST['fechaInicio2'] : null,
                    'fin' => isset($_POST['fechaFin2']) ? $_POST['fechaFin2'] : null,
                ],
                3 => [
                    'inicio' => isset($_POST['fechaInicio3']) ? $_POST['fechaInicio3'] : null,
                    'fin' => isset($_POST['fechaFin3']) ? $_POST['fechaFin3'] : null,
                ]
            ];

            $errors = [];

            if (empty($nombrePeriodo)) {
                $errors[] = 'El campo "Periodo" es obligatorio.';
            }
            if ($actual === null || ($actual != 0 && $actual != 1)) {
                $errors[] = 'El campo "Actual" es obligatorio.';
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header('Location: ' . BASE_URL . '/registroPeriodo.php');
                exit();
            }

            $resultado = $this->periodoModel->createPeriodo($nombrePeriodo, $actual, $fechas);

            if ($resultado) {
                $_SESSION['message'] = "Periodo registrado exitosamente.";
                header('Location: ' . BASE_URL . '/administrarPeriodosEscolares.php');
                exit();
            } else {
                if (isset($_SESSION['message'])) {
                    $_SESSION['errors'] = [$_SESSION['message']];
                    unset($_SESSION['message']);
                } else {
                    $_SESSION['errors'] = ["Error al registrar el periodo escolar."];
                }

                header('Location: ' . BASE_URL . '/registroPeriodo.php');
                exit();
            }
        } else {
            header('Location: ' . BASE_URL . '/registroPeriodo.php');
            exit();
        }
    }

    public function showEditForm()
    {
        session_start();

        $rolesPermitidos = [3];
        if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/cerrarSesion.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idPeriodo = isset($_POST['idPeriodo']) ? intval($_POST['idPeriodo']) : 0;

            if ($idPeriodo > 0) {
                $periodo = $this->periodoModel->getPeriodoById($idPeriodo);

                if ($periodo) {
                    $_SESSION['periodo'] = $periodo;

                    if (empty($_SESSION['csrf_token'])) {
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    }

                    $csrf_token = $_SESSION['csrf_token'];
                    $user = $_SESSION['user'];

                    require_once '../views/editarPeriodo.php';
                } else {
                    $_SESSION['message'] = 'Periodo no encontrado';
                    header('Location: ' . BASE_URL . '/administrarPeriodosEscolares.php');
                    exit();
                }
            } else {
                $_SESSION['message'] = 'ID de periodo inválido';
                header('Location: ' . BASE_URL . '/administrarPeriodosEscolares.php');
                exit();
            }
        } else {
            header('Location: ' . BASE_URL . '/administrarPeriodosEscolares.php');
            exit();
        }
    }

    public function updatePeriodo()
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

            $idPeriodo = isset($_POST['idPeriodo']) ? intval($_POST['idPeriodo']) : 0;
            $nombrePeriodo = isset($_POST['periodo']) ? trim($_POST['periodo']) : null;
            $actual = isset($_POST['actual']) ? intval($_POST['actual']) : null;

            $errors = [];

            if ($idPeriodo <= 0) {
                $errors[] = 'ID de periodo inválido.';
            }

            if (empty($nombrePeriodo)) {
                $errors[] = 'El campo "Periodo" es obligatorio.';
            }

            if ($actual === null || ($actual != 0 && $actual != 1)) {
                $errors[] = 'El campo "Actual" es obligatorio.';
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['periodo'] = ['idPeriodo' => $idPeriodo, 'periodo' => $nombrePeriodo, 'actual' => $actual];
                $user = $_SESSION['user'];
                $csrf_token = $_SESSION['csrf_token'];
                
                require_once '../views/editarPeriodo.php';
                exit();
            }

            $resultado = $this->periodoModel->updatePeriodo($idPeriodo, $nombrePeriodo, $actual);

            if ($resultado) {
                $_SESSION['message'] = "Periodo actualizado exitosamente.";
                header('Location: ' . BASE_URL . '/administrarPeriodosEscolares.php');
                exit();
            } else {
                if (isset($_SESSION['message'])) {
                    $_SESSION['errors'][] = $_SESSION['message'];
                    unset($_SESSION['message']);
                } else {
                    $_SESSION['errors'][] = "Error al actualizar el periodo escolar.";
                }

                $_SESSION['periodo'] = ['idPeriodo' => $idPeriodo, 'periodo' => $nombrePeriodo, 'actual' => $actual];
                $user = $_SESSION['user'];
                $csrf_token = $_SESSION['csrf_token'];
                
                require_once '../views/editarPeriodo.php';
                exit();
            }
        } else {
            header('Location: ' . BASE_URL . '/administrarPeriodosEscolares.php');
            exit();
        }
    }

    public function deletePeriodo()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                echo json_encode(['status' => 'error', 'message' => 'Token CSRF inválido.']);
                exit();
            }

            $rolesPermitidos = [3];
            if (!isset($_SESSION['user']) || !in_array($_SESSION["rol"], $rolesPermitidos)) {
                echo json_encode(['status' => 'error', 'message' => 'Acceso denegado.']);
                exit();
            }

            $idPeriodo = isset($_POST['idPeriodo']) ? intval($_POST['idPeriodo']) : 0;

            if ($idPeriodo > 0) {
                $resultado = $this->periodoModel->deletePeriodo($idPeriodo);

                if ($resultado) {
                    echo json_encode(['status' => 'success', 'message' => 'Periodo escolar eliminado con éxito.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el periodo escolar.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ID de periodo inválido.']);
            }
        } else {
            header('Location: ' . BASE_URL . '/administrarPeriodosEscolares.php');
            exit();
        }
    }
}