<?php
/**
 * __autoload magic php function
 * This function will include files automatically avoiding the use of require(_once)
 * The path is constructed from the namespace of the class
 * @param string $class
 */
function __autoload($class){
    $path = str_replace('\\', '/', $class).'.class.php';
    require $path;
}