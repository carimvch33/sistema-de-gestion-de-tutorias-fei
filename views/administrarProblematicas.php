<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Administración de problemáticas académicas</title>
    <link rel="stylesheet" href="assets/css/administrarProblematicas.css">
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
        <button class="buttonNew" onclick="location.href = './registroProblematica.php' "><i class="fas fa-plus"></i> Nuevo</button>
    </div>

    <div class="table-container">
        <table id="problematicaTable">
            <thead>
                <tr>
                    <th class="autoWidthColumn">Descripción</th>
                    <th class="autoWidthColumn">Tipo de problemática</th>
                    <th class="autoWidthColumn">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($problematicas)) {
                    foreach ($problematicas as $row) {
                        $idProblematica = $row['idProblematica'];
                        echo "<tr>
                                <td>{$row['descripcion']}</td>
                                <td>{$row['tipoProblematica']}</td>
                                <td class='action-buttons autoTable'>
                                    <button class='edit' data-id-problematica='{$idProblematica}'><i class='fas fa-edit'></i></button>
                                    <button class='delete' data-id-problematica='{$idProblematica}' data-csrf-token='{$csrf_token}'><i class='fas fa-trash-alt'></i></button>
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
    <script src="assets/js/administrarProblematicas.js"></script>
</body>

</html>