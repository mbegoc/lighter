<?php
/*
 * Root file of the entire application. Here land the requests.
 * This file purpose is only to instanciate the Router which will
 * actually handle the requests.
 */
require '../config/main.php';


$router = new lighter\handlers\Router();
$router->execute();

