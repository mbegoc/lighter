<?php
namespace lighter\helpers\html\forms;


/**
 * an input text
 *
 * @name InputText
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
class InputText extends Input {


	/**
     * default constructor
	 *
	 * @param string $name
	 * @param string $label
	 * @param int $size
	 * @param mixed $value
	 */
	public function __construct($name, $label, $size, $value = "", $type = FormElement::STRING){
	    parent::__construct('text', $type, $name, $label, $size, $value);
	}

}


