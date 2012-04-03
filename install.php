<?php
define('ROOT_PATH', '/home/michel/Developpement/www');
define('RELATIVE_PATH', '/');
define('USER_CONTROL_PATH', 'myApp/controllers/');
define('USER_CONTROL_PACK', '\\myApp\\controllers\\');
define('USER_TEMPLATES_PATH', 'myApp/views/templates/');
define('USER_TEMPATES_EXT', '.tpl.php');


error_reporting(E_ALL);
require "lighter/config/include.php";


use lighter\models\Config;

use lighter\routing\routes\ControllerNode;
use lighter\routing\routes\FixedNode;
use lighter\routing\routes\MethodNode;
use lighter\routing\routes\ParamNode;
use lighter\routing\routes\ParamsNode;
use lighter\routing\routes\RootNode;


$config = Config::getInstance();
$config->setTemplatesPaths(array(
    USER_TEMPLATES_PATH => USER_TEMPATES_EXT,
	'lighter/views/templates/' => '.tpl.php',
));
$config->setApplicationPath(ROOT_PATH, RELATIVE_PATH);
$config->setDefaultController('Admin', 'handleRequest');
$config->setControllersPaths(array(
    USER_CONTROL_PATH => USER_CONTROL_PACK,
	'lighter/controllers/' => '\\lighter\\controllers\\',
));
$config->setDebugData('lighter/config/debug.xml', true);
$config->setSessionData(1200, true);
$config->addLanguage('en', 'English');
$config->addLanguage('fr', 'Français');
$config->setIndexFile(true);




$map = new RootNode('/');
$map->addSubNode(new ControllerNode('Admin'))
    ->addSubNode(new MethodNode('handleRequest'))
    ->addSubNode(new ParamsNode());

$config->setRoutes($map);

if ($config->save()) {
    echo 'ok';
} else {
    echo 'not ok';
}

