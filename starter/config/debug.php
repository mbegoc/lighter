<?php
// debugging configuration information
use lighter\handlers\Config;

$config = Config::getInstance();

if (LIGHTER_ENVIRONMENT === 'production') {
    $config->setValue('debug', 'active', false);
} else {
    $config->setSection('debug', array(
        'active' => true,
        'redirect' => false,
        'report' => false,
        'frameReport' => true,
        'reportFile' => 'debug.html',
        'scriptPath' => '/tmp/',
        'sections' => array('default'),
    ));
}