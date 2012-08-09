<?php
use lighter\handlers\Config;


/*
 * Root file of the entire application. Here land the requests.
 * This file purpose is to intiate lighter and the application,
 * and then to instanciate the Router which will actually handle
 * the requests.
 */
require '/home/michel/Developpement/www/LIB/lighter/config/init.php';
Config::getInstance()->initApp('../config/main.php');


$router = new lighter\handlers\Router();
$router->execute();

