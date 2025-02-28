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
            'icon' => 'importar-icon.png',
        ],
        [
            'title' => 'Registro de Reporte de Tutorías',
            'link' => BASE_URL . '/administrarReportes.php',
            'icon' => 'carrera-icon.png',
        ],
    ],
    2 => [
    ],
    3 => [ // Administrador
        [
            'title' => 'Importación de datos',
            'link' => BASE_URL . '/importarDatos.php',
            'icon' => 'importar-icon.png',
        ],
        [
            'title' => 'Carreras',
            'link' => BASE_URL . '/administrarCarreras.php',
            'icon' => 'carrera-icon.png',
        ],
        [
            'title' => 'Periodos escolares',
            'link' => BASE_URL . '/administrarPeriodosEscolares.php',
            'icon' => 'periodo-icon.png',
        ],
        [
            'title' => 'Experiencias educativas',
            'link' => BASE_URL . '/administrarExperienciasEducativas.php',
            'icon' => 'rol-icon.png',
        ],
        [
            'title' => 'Secciones',
            'link' => BASE_URL . '/administrarSecciones.php',
            'icon' => 'rol-icon.png',
        ],
        [
            'title' => 'Problemáticas académicas',
            'link' => BASE_URL . '/administrarProblematicas.php',
            'icon' => 'problematica-icon.png',
        ],
        [
            'title' => 'Tipos de problemáticas',
            'link' => BASE_URL . '/administrarTiposProblematicas.php',
            'icon' => 'problematica-tipo-icon.png',
        ],
        [
            'title' => 'Estudiantes',
            'link' => BASE_URL . '/administrarEstudiantes.php',
            'icon' => 'estudiante-icon.png',
        ],
        [
            'title' => 'Profesores',
            'link' => BASE_URL . '/administrarProfesores.php',
            'icon' => 'profesor-icon.png',
        ],
        [
            'title' => 'Coordinadores',
            'link' => BASE_URL . '/administrarCoordinadores.php',
            'icon' => 'coordinador-icon.png',
        ],
        [
            'title' => 'Jefes de Carrera',
            'link' => BASE_URL . '/administrarJefesCarrera.php',
            'icon' => 'jefe_carrera-icon.png',
        ],
        [
            'title' => 'Administradores',
            'link' => BASE_URL . '/administrarAdministradores.php',
            'icon' => 'administrador-icon.png',
        ],
        [
            'title' => 'Actualizar roles',
            'link' => BASE_URL . '/actualizarRol.php',
            'icon' => 'rol-icon.png',
        ],
    ],
    4 => [ // Coordinador
        [
            'title' => 'Consulta de Tutorías',
            'link' => BASE_URL . '/consultarTutorias.php',
            'icon' => 'importar-icon.png',
        ],
        [
            'title' => 'Registro de Sesión de Tutorías',
            'link' => BASE_URL . '/tutorias.php',
            'icon' => 'importar-icon.png',
        ],
        [
            'title' => 'Registro de Reporte de Tutorías',
            'link' => BASE_URL . '/administrarReportes.php',
            'icon' => 'carrera-icon.png',
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
