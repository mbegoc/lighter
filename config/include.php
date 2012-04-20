<?php
/**
 * __autoload magic php function
 * This function will include files automatically avoiding the use of require(_once)
 * The path is constructed from the namespace of the class
 * @param string $class
 */

if (defined('LIGHTER_PATH')) {
    $packagesRoots = array('lighter' => LIGHTER_PATH.'lighter');

    function __autoload($class) {
        global $packagesRoots;
        $packageClass = explode('\\', $class);
        if (isset($packagesRoots[$packageClass[0]])) {
            $packageClass[0] = $packagesRoots[$packageClass[0]];
        }
        $path = implode('/', $packageClass).'.php';
        require $path;
    }

} else {
    define('LIGHTER_PATH', './');

    function __autoload($class) {
        $path = str_replace('\\', '/', $class).'.php';
        require $path;
    }

}

