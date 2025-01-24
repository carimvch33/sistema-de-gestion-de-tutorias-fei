<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Administración de experiencias educativas</title>
    <link rel="stylesheet" href="assets/css/administrarExperienciasEducativas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="libs/DataTables/datatables.min.css" rel="stylesheet">
</head>

<body>
    <div class="header-container">
        <div class="header-left">
            <img src="assets/img/UV.png" alt="UV Logo">
            <div class="welcome-message">Bienvenid@ <?php echo htmlspecialchars($user); ?></div>
        </div>
        <div class="header-left">
            <button class="buttonsHead" onclick="location.href='/menu.php'"><i class="fas fa-home"></i> Inicio</button>
            <button class="buttonsHead" onclick="location.href='/cerrarSesion.php'"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</button>
        </div>
    </div>

    <div class="new-button-container">
        <button class="buttonNew" onclick="location.href = './registroExperienciaEducativa.php' "><i class="fas fa-plus"></i> Nuevo</button>
    </div>

    <div class="table-container">
        <table id="experienciasTable">
            <thead>
                <tr>
                    <th class="autoWidthColumn">Experiencia Educativa</th>
                    <th class="autoWidthColumn">Programa educativo</th>
                    <th class="autoWidthColumn">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($experiencias)) {
                    foreach ($experiencias as $row) {
                        $idExperiencia = $row['idExperienciaEducativa'];
                        echo "<tr>
                                <td>{$row['nombreEE']}</td>
                                <td>{$row['nombrePrograma']}</td>
                                <td class='action-buttons autoTable'>
                                    <button class='edit' data-id-experiencia='{$idExperiencia}'><i class='fas fa-edit'></i></button>
                                    <button class='delete' data-id-experiencia='{$idExperiencia}' data-csrf-token='{$csrf_token}'><i class='fas fa-trash-alt'></i></button>
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
    <script src="libs/DataTables/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/administrarExperienciasEducativas.js"></script>
</body>

</html>