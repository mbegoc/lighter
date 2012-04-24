<?php
/*
 * main configuration file of a lighter application.
 *
 */
error_reporting(E_ALL);


// environment type: production, staging, dev for example
// unkown is the default value
$lighter_env = getenv('LIGHTER_ENVIRONMENT');
if ($lighter_env !== false) {
    define('LIGHTER_ENVIRONMENT', $lighter_env);
} else {
    define('LIGHTER_ENVIRONMENT', 'unknown');
}


// constants declaration
define('ROOT_APP_PATH', '.');
define('LIGHTER_PATH', '.');


// inclusions
require LIGHTER_PATH.'config/include.php';

use lighter\handlers\Config;

use lighter\routing\routes\ControllerNode;
use lighter\routing\routes\FixedNode;
use lighter\routing\routes\MethodNode;
use lighter\routing\routes\ParamNode;
use lighter\routing\routes\ParamsNode;
use lighter\routing\routes\RootNode;
use lighter\routing\routes\StaticFileNode;


// main config, i.e. very basic config
$config = Config::getInstance();

$config->setDbData('mongo', array(
    'host' => 'localhost',
    'port' => '27017',
	'database' => 'lighter',
));

$config->setSection('controllersPaths', array(
	'myApp/controllers/' => '\\myApp\\controllers\\',
    LIGHTER_PATH.'lighter/controllers/' => '\\lighter\\controllers\\',
));

$config->setSection('tplPaths', array(
    'myApp' => 'myApp/views/templates/',
    'lighter' => LIGHTER_PATH.'lighter/views/templates/',
));

// define routes
$map = new RootNode('/');
$map->addSubNode(new StaticFileNode('include'));
$map->addSubNode(new ControllerNode('Example'))
    ->addSubNode(new MethodNode('handleRequest'))
    ->addSubNode(new ParamsNode());

$config->setRoutes($map);


// additional configuration inclusion
require '../config/debug.php';

