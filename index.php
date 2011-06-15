<?php
error_reporting(E_ALL);
require "pmf/config/include.php";


$router = new controllers\MainController();
$router->execute();

