<?php
namespace lighter\exceptions;


/**
 * Exception thrown when an object initialization function is called but the object
 * is already in use.
 *
 * @name AlreadyInUseException
 * @package lighter
 * @subpackage exceptions
 * @since 0.1.1
 * @version 0.1.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class AlreadyInUseException extends \Exception {}

