<?php
namespace pmf\helpers;


/**
 * a class to work with Exception instead of error in PHP
 *
 * @name Exception
 * @abstract
 * @package pmf
 * @subpackage helpers
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
abstract class Exception {
    /**
     * the error level we want to convert to Exception
     *
     * @var int
     */
    protected static $errorLevel;


    /**
     * convert errors to exceptions
     *
     * @static
     */
    public static function convertErrorToException($errorLevel = E_ALL){
        self::$errorLevel = $errorLevel;
        set_error_handler(array("pmf\helpers\Exception", "error_handler"));
    }


    /**
     * restore the default PHP error handler
     *
     * @static
     */
    public static function restoreError(){
        restore_error_handler();
    }


    /**
     * a replacement error handler for the default PHP one
     *
     * @static
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


