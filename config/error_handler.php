<?php
// DEF-32: Manejador centralizado de errores de base de datos

if (isProduction()) {
    // En producción: No mostrar errores al usuario, solo registrarlos
    ini_set('display_errors', '0');
    error_reporting(E_ALL);
} else {
    // En desarrollo: Mostrar todos los errores
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

// Manejar excepciones relacionadas con la base de datos
set_exception_handler(function ($exception) {
    $isDatabaseError = false;

    if ($exception instanceof mysqli_sql_exception) {
        $isDatabaseError = true;
    }
    
    $message = $exception->getMessage();
    if (stripos($message, 'database') !== false || 
        stripos($message, 'connection') !== false ||
        stripos($message, 'mysqli') !== false ||
        stripos($message, 'conexión') !== false ||
        stripos($message, 'base de datos') !== false) {
        $isDatabaseError = true;
    }
    
    // Manejar solo errores de base de datos
    if ($isDatabaseError) {
        error_log("ERROR DE BASE DE DATOS: " . $exception->getMessage());
        error_log("Archivo: " . $exception->getFile() . " - Línea: " . $exception->getLine());
        error_log("Stack trace: " . $exception->getTraceAsString());

        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        if ($isAjax) {
            header('Content-Type: application/json');
            http_response_code(500);
            
            echo json_encode([
                'status' => 'error',
                'message' => 'Error de conexión con la base de datos. Por favor, intente nuevamente más tarde.'
            ]);
        } else {
            http_response_code(500);
            require_once __DIR__ . '/../views/error_page.php';
        }
        
        exit();
    }
});

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }

    error_log("ERROR PHP [$errno]: $errstr en $errfile:$errline");
    
    return false;
});

// Manejar errores fatales de base de datos
register_shutdown_function(function () {
    $error = error_get_last();
    
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $isDatabaseError = stripos($error['message'], 'mysqli') !== false ||
                          stripos($error['message'], 'database') !== false ||
                          stripos($error['message'], 'connection') !== false;
        
        if ($isDatabaseError) {
            error_log("ERROR FATAL DE BD: " . $error['message'] . " en " . $error['file'] . ":" . $error['line']);

            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
            
            if ($isAjax) {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error de conexión con la base de datos.'
                ]);
            } else {
                http_response_code(500);
                require_once __DIR__ . '/../views/error_page.php';
            }
            exit();
        }
    }
});
?>
