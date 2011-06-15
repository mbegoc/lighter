<?php
/*****************************************************************************
 * Copyright (c) 2010 Michel Begoc
 * Author: Michel Begoc
 * Date: 2010-06-16
 * Description: 
 * 
 *****************************************************************************/
require_once("forms/FormElement.class.php");


class Hidden extends FormElement {

	public function __construct($name, $value = ""){
		$this->name = $name;
		$this->value = $value;
	}
	
	
	/**
	 * retourne le html correspondant à l'élément
	 * @return string html
	 */
	public function toHTML(){
		return "<input type='hidden' name='$this->name' id='$this->name' value='$this->value'/>";
	}
}
/*****************************************************************************
 * End of file HiddenField.class.php
 */