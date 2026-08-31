<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Sistema de Tutorías</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 10px;
            background-color: #f0f0f0;
            border-bottom: 2px solid #ddd;
        }

        .header-left {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .header-left img {
            width: 120px;
        }
        
        .error-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 30px 40px;
            max-width: 700px;
            width: 82%;
            margin: 80px auto 30px auto;
            text-align: center;
        }
        
        .error-icon {
            font-size: 60px;
            color: #e74c3c;
            margin-bottom: 15px;
        }
        
        h1 {
            color: #18529D;
            font-size: 24px;
            margin-bottom: 15px;
            font-weight: bold;
        }
        
        p {
            color: #333;
            font-size: 15px;
            line-height: 1.5;
            margin-bottom: 12px;
        }

        footer {
            background-color: #18529D;
            color: white;
            text-align: center;
            padding: 8px;
            margin-top: auto;
        }
    </style>
</head>
<body>
    <div class="header-container">
        <div class="header-left">
            <img src="<?= BASE_URL; ?>/assets/img/UV.png" alt="UV Logo">
        </div>
    </div>

    <div class="error-container">
        <div class="error-icon">⚠️</div>
        <h1>¡Oops! Algo salió mal</h1>
        <p>
            Ha ocurrido un error inesperado en el sistema.
        </p>
        <p>
            Por favor, intenta nuevamente en unos momentos. 
            Si el problema persiste, contacta al administrador del sistema.
        </p>
    </div>

    <footer>© Universidad Veracruzana</footer>
</body>
</html>