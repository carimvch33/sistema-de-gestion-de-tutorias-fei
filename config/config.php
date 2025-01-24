<?php
define('ENVIRONMENT', 'development'); // development || production
define('BASE_URL', '/agendatutorias'); //agendatutorias
define('DB_HOST', '10.37.129.2'); //localhost

function isProduction()
{
    return ENVIRONMENT === 'production';
}
?>