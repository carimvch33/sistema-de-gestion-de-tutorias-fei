<?php
define('ENVIRONMENT', 'development');
define('BASE_URL', '/agendatutorias');

function isProduction()
{
    return ENVIRONMENT === 'production';
}
?>