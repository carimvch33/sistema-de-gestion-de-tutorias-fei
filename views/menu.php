<?php
require_once '../config/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || !isset($_SESSION['rol'])) {
    header('Location: /cerrarSesion.php');
    exit();
}

$rol = $_SESSION['rol'];

if ($rol == 2) {
    header('Location: sesionesTutoria.php');
    exit();
}


$menuOptions = [
    1 => [ // Tutor
        [
            'title' => 'Registro de Sesión de Tutorías',
            'link' => BASE_URL . '/tutorias.php',
            'icon' => 'import-icon.svg',
        ],
        [
            'title' => 'Registro de Reporte de Tutorías',
            'link' => BASE_URL . '/administrarReportes.php',
            'icon' => 'career-icon.svg',
        ],
    ],
    2 => [
    ],
    3 => [ // Administrador
        [
            'title' => 'Importación de datos',
            'link' => BASE_URL . '/importarDatos.php',
            'icon' => 'import-icon.svg',
        ],
        [
            'title' => 'Carreras',
            'link' => BASE_URL . '/administrarCarreras.php',
            'icon' => 'career-icon.svg',
        ],
        [
            'title' => 'Periodos escolares',
            'link' => BASE_URL . '/administrarPeriodosEscolares.php',
            'icon' => 'period-icon.svg',
        ],
        [
            'title' => 'Experiencias educativas',
            'link' => BASE_URL . '/administrarExperienciasEducativas.php',
            'icon' => 'role-icon.svg',
        ],
        [
            'title' => 'Secciones',
            'link' => BASE_URL . '/administrarSecciones.php',
            'icon' => 'role-icon.svg',
        ],
        [
            'title' => 'Problemáticas académicas',
            'link' => BASE_URL . '/administrarProblematicas.php',
            'icon' => 'problem-icon.svg',
        ],
        [
            'title' => 'Tipos de problemáticas',
            'link' => BASE_URL . '/administrarTiposProblematicas.php',
            'icon' => 'problem_type-icon.svg',
        ],
        [
            'title' => 'Estudiantes',
            'link' => BASE_URL . '/administrarEstudiantes.php',
            'icon' => 'student-icon.svg',
        ],
        [
            'title' => 'Profesores',
            'link' => BASE_URL . '/administrarProfesores.php',
            'icon' => 'teacher-icon.svg',
        ],
        [
            'title' => 'Coordinadores',
            'link' => BASE_URL . '/administrarCoordinadores.php',
            'icon' => 'coordinator-icon.svg',
        ],
        [
            'title' => 'Jefes de Carrera',
            'link' => BASE_URL . '/administrarJefesCarrera.php',
            'icon' => 'career_manager-icon.svg',
        ],
        [
            'title' => 'Administradores',
            'link' => BASE_URL . '/administrarAdministradores.php',
            'icon' => 'administrator-icon.svg',
        ],
        [
            'title' => 'Actualizar roles',
            'link' => BASE_URL . '/actualizarRol.php',
            'icon' => 'role-icon.svg',
        ],
    ],
    4 => [ // Coordinador
        [
            'title' => 'Consulta de Tutorías',
            'link' => BASE_URL . '/consultarTutorias.php',
            'icon' => 'import-icon.svg',
        ],
        [
            'title' => 'Consulta de Reportes de Tutorías',
            'link' => BASE_URL . '/consultarReportes.php',
            'icon' => 'career-icon.svg',
        ],
        [
            'title' => 'Registro de Sesión de Tutorías',
            'link' => BASE_URL . '/tutorias.php',
            'icon' => 'import-icon.svg',
        ],
        [
            'title' => 'Registro de Reporte de Tutorías',
            'link' => BASE_URL . '/administrarReportes.php',
            'icon' => 'career-icon.svg',
        ],
    ],
];

$rol = $_SESSION['rol'];
$options = isset($menuOptions[$rol]) ? $menuOptions[$rol] : [];

if (empty($options)) {
    echo "No tiene permisos para acceder a este menú.";
    exit();
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Menú Principal UV</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="header-container">
        <div class="header-left">
            <img src="<?= BASE_URL; ?>/assets/img/UV.png" alt="UV Logo">
            <div class="welcome-message">Bienvenid@ <?php echo htmlspecialchars($user); ?></div>
        </div>
        <div class="header-left">
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/menu.php'"><i class="fas fa-home"></i> Inicio</button>
            <button class="buttonsHead" onclick="location.href='<?= BASE_URL; ?>/cerrarSesion.php'"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</button>
        </div>
    </div>

    <div class="button-container">
        <?php foreach ($options as $option): ?>
            <button class="button-style" onclick="location.href='<?= htmlspecialchars($option['link']) ?>'">
                <img src="<?= BASE_URL; ?>/assets/img/<?= htmlspecialchars($option['icon']) ?>" alt="">
                <span><?= htmlspecialchars($option['title']) ?></span>
            </button>
        <?php endforeach; ?>
    </div>

    <footer>© Universidad Veracruzana</footer>
</body>
</html>
