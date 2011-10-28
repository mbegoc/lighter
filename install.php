<?php
error_reporting(E_ALL);
require "pmf/config/include.php";


use pmf\dto\Config;
use pmf\dto\DBAccessor;


$config = new Config();
$config->setTemplateData('views/templates/', '.tpl.php');
$config->setApplicationPath('/home/michel/Developpement/www', '/LIB/pmf/');
$config->setDefaultController('Content', 'handleRequest');
$config->setDefaultView('MainView');
$config->setDebugData('pmf/config/debug.xml', true);
$config->setSessionData(1200, true);
$config->addLanguage('en', 'English');
$config->addLanguage('fr', 'Français');

$dba = new DBAccessor('config');
$dba->save($config);