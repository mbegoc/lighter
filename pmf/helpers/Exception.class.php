<?php
namespace helpers;


/**
 * a class to work with Exception instead of error in PHP
 * @author michel
 *
 */
abstract class Exception {
    /**
     * the error level we want to convert to Exception
     * @var int
     */
    protected static $errorLevel;


    /**
     * convert errors to exceptions
     */
    public static function convertErrorToException($errorLevel = E_ALL){
        self::$errorLevel = $errorLevel;
        set_error_handler(array("helpers\Exception", "error_handler"));
    }


    /**
     * restore the default PHP error handler
     */
    public static function restoreError(){
        restore_error_handler();
    }


    /**
     * a replacement error handler for the default PHP one
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     */
    public static function error_handler($errno, $errstr, $errfile, $errline ) {
        if(($errno & self::$errorLevel) != 0){
            $error = new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        }
    }

}

