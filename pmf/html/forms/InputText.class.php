<?php
namespace html\forms;


/**
 * an input text
 * @author michel
 *
 */
class InputText extends Input {


	/**
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

