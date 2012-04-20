<?php
namespace lighter\helpers\html\forms;


/**
 * a password input element
 *
 * @name Password
 * @package lighter
 * @subpackage helpers\html\forms
 * @see lighter\helpers\html\forms\Input
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class Password extends Input {


    /**
     * default constructor
     *
     * @param string $name
     * @param string $label
     * @param int $size
     */
    public function __construct($name, $label, $size) {
        parent::__construct('password', FormElement::STRING, $name, $label, $size, 'xxxxxxxx');
    }

}


