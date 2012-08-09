<?php
error_reporting(E_ALL);


// environment type: production, staging, dev for example
// unkown is the default value
$lighter_env = getenv('LIGHTER_ENVIRONMENT');
if ($lighter_env !== false) {
    define('LIGHTER_APP_ENV', $lighter_env);
} else {
    define('LIGHTER_APP_ENV', 'unknown');
}


define('LIGHTER_PATH', realpath(__DIR__.'/..'));


// inclusions
require LIGHTER_PATH.'/lighter/handlers/Config.php';
require LIGHTER_PATH.'/config/include.php';
require LIGHTER_PATH.'/config/version.php';

