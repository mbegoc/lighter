<?php
use handlers\Debug;


error_reporting(E_ALL);
require "pmf/config/include.php";


$router = new handlers\Router();
$router->execute();

