<?php
define('ENVIRONMENT', 'development');
define('BASE_URL', '/agendatutorias'); //agendatutorias
define('DB_HOST', '177.244.14.25'); //localhost

function isProduction()
{
    return ENVIRONMENT === 'production';
}
?>