<?php
define('ENVIRONMENT', 'development');
define('BASE_URL', ''); //agendatutorias
define('DB_HOST', '10.37.129.2'); //localhost

function isProduction()
{
    return ENVIRONMENT === 'production';
}
?>