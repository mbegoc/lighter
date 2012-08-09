<?php
use lighter\handlers\Config;

use lighter\routing\routes\ControllerNode;
use lighter\routing\routes\FixedNode;
use lighter\routing\routes\MethodNode;
use lighter\routing\routes\ParamNode;
use lighter\routing\routes\ParamsNode;
use lighter\routing\routes\RootNode;
use lighter\routing\routes\StaticFileNode;

use lighter\handlers\Debug;
use lighter\handlers\Logger;


// main config, i.e. very basic config
$config = Config::getInstance();

$config->addControllersSpace('\\your_app_name\\controllers\\');
$config->addTemplatePath('your_app_name/views/templates/');

$config->setSection('mongo', array(
    'default' => array(
        'host' => 'localhost',
        'port' => '27017',
    	'database' => 'your_app_mongo_name',
	),
));

// define routes
$map = new RootNode('/');
$map->addSubNode(new ControllerNode('DefaultController'))
    ->addSubNode(new MethodNode('handleRequest'))
    ->addSubNode(new ParamsNode());
$config->setRoutes($map);

$config->setSection('log', array(
    'active' => true,
    'level' => Logger::ERROR,
    'path' => LIGHTER_APP_PATH.'/logs/',
));

