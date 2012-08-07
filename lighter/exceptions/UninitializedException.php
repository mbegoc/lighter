<?php
namespace lighter\exceptions;


/**
 * Exception thrown when an object that need specific intialization is tryed to
 * be used without have been correctly set.
 *
 * @name UninitializedException
 * @package lighter
 * @subpackage exceptions
 * @since 0.1.1
 * @version 0.1.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class UninitializedException extends \Exception {}

