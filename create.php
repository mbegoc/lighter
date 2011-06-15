<?php
error_reporting(E_ALL);
require "pmf/config/include.php";


use dto\Config;
use dto\DBAccessor;


$config = new Config();
$config->setTemplateData('view/templates/', '.tpl.php');
$config->setApplicationPath('/home/michel/Developpement/www', '/LIB/pmf/');
$config->setDefaultController('Content', 'handleRequest');
$config->setDefaultView('MainView', 'display');
$config->setSessionData(1200, true);
$config->addLanguage('en', 'English');
$config->addLanguage('fr', 'Français');

$dba = new DBAccessor('config');
$dba->save($config);