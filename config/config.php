<?php
define('ENVIRONMENT', 'development');

function isProduction()
{
    return ENVIRONMENT === 'production';
}
?>