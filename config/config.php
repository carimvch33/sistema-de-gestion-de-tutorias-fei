<?php
define('ENVIRONMENT', 'development'); // development || production
define('BASE_URL', ''); //agendatutorias
define('DB_HOST', 'localhost'); //localhost

function isProduction()
{
    return ENVIRONMENT === 'production';
}
?>