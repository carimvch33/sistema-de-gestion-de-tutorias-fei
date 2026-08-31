<?php
require_once '../config/config.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Administración de experiencias educativas</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/administrarExperienciasEducativas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="<?= BASE_URL; ?>/libs/DataTables/datatables.min.css" rel="stylesheet">
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

    <!-- Contenedor de alertas -->
    <div class="container mt-3" style="width: 82%; margin: 0 auto;">
        <?php
        if (isset($_SESSION['errors'])) {
            echo '<div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 10px;">';
            foreach ($_SESSION['errors'] as $error) {
                echo "<p style='margin: 0;'>$error</p>";
            }
            echo '</div>';
            unset($_SESSION['errors']);
        }

        if (isset($message)) {
            echo '<div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 10px;">';
            echo "<p style='margin: 0;'>{$message}</p>";
            echo '</div>';
        }
        ?>
    </div>

    <div class="new-button-container" style="display: flex; gap: 15px; margin-bottom: 20px; justify-content: flex-end; align-items: center;">
        
        <!-- Formulario de Importación con texto de ayuda -->
        <form action="<?= BASE_URL; ?>/importarExperiencias.php" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 5px; margin: 0; background-color: #f8f9fa; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <small style="color: #6c757d; font-size: 12px;"><b>Formato requerido CSV:</b> DOCENTE, EXPERIENCIA EDUCATIVA, NRC</small>
            <div style="display: flex; align-items: center; gap: 10px;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                
                <select name="idCarrera" class="form-control" style="width: auto; font-size: 14px; padding: 5px;" required>
                    <option value="" disabled selected>-- Elige Carrera --</option>
                    <?php if(!empty($programas)) { foreach ($programas as $prog): ?>
                        <option value="<?= $prog['idCarrera'] ?>"><?= htmlspecialchars($prog['carrera'] ?? $prog['nombre']) ?></option>
                    <?php endforeach; } ?>
                </select>

                <select name="idPeriodo" class="form-control" style="width: auto; font-size: 14px; padding: 5px;" required>
                    <option value="" disabled selected>-- Elige Periodo --</option>
                    <?php if(!empty($periodos)) { foreach ($periodos as $per): ?>
                        <option value="<?= $per['idPeriodo'] ?>"><?= htmlspecialchars($per['periodo'] ?? $per['nombre']) ?></option>
                    <?php endforeach; } ?>
                </select>

                <input type="file" name="csv_materias" accept=".csv" required style="font-size: 14px; max-width: 200px;">
                
                <button type="submit" class="buttonNew" style="background-color: #17a2b8;" title="Sube CSV">
                    <i class="fas fa-file-csv"></i> Importar CSV
                </button>
            </div>
        </form>

        <button class="buttonNew" onclick="location.href = '<?= BASE_URL; ?>/registroExperienciaEducativa.php' ">
            <i class="fas fa-plus"></i> Registrar Materia
        </button>
    </div>

    <div class="table-container">
        <table id="experienciasTable">
            <thead>
                <tr>
                    <th>Experiencia Educativa</th>
                    <th>Programa educativo</th>
                    <th>NRC</th>
                    <th>Profesor</th>
                    <th>Periodo</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($experiencias)) {
                    foreach ($experiencias as $row) {
                        $idExperiencia = $row['idExperienciaEducativa'];
                        
                        $nrc = !empty($row['nrc']) ? $row['nrc'] : '<span class="text-danger" style="font-weight:bold;">Sin asignar</span>';
                        $profesor = !empty($row['tutorNombre']) ? $row['tutorNombre'] : '<span class="text-danger" style="font-weight:bold;">Sin asignar</span>';
                        $periodo = !empty($row['periodoNombre']) ? $row['periodoNombre'] : '<span class="text-danger" style="font-weight:bold;">Sin asignar</span>';

                        echo "<tr>
                                <td>{$row['nombreEE']}</td>
                                <td>{$row['nombrePrograma']}</td>
                                <td>{$nrc}</td>
                                <td>{$profesor}</td>
                                <td>{$periodo}</td>
                                <td class='action-buttons autoTable'>
                                    <button class='edit' data-id-experiencia='{$idExperiencia}' title='Editar Registro Completo'><i class='fas fa-edit'></i></button>
                                    <button class='delete' data-id-experiencia='{$idExperiencia}' data-csrf-token='{$csrf_token}' title='Eliminar Registro Completo'><i class='fas fa-trash-alt'></i></button>
                                </td>
                            </tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <footer>© Universidad Veracruzana</footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?= BASE_URL; ?>/libs/DataTables/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= BASE_URL; ?>/assets/js/administrarExperienciasEducativas.js"></script>
</body>

</html>