<?php
namespace html\forms;


/**
 * a password input element
 * @author michel
 *
 */
class Password extends Input {


	/**
	 *
	 * @param string $name
	 * @param string $label
	 * @param int $size
	 */
	public function __construct($name, $label, $size){
	    parent::__construct('password', FormElement::STRING, $name, $label, $size, 'xxxxxxxx');
	}

}

