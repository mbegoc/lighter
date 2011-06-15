<?php
/*****************************************************************************
 * Copyright (c) 2010 Michel Begoc
 * Author: Michel Begoc
 * Date: 2010-06-16
 * Description: 
 * 
 *****************************************************************************/
require_once("forms/FormElement.class.php");


class InputText extends FormElement {
	private $size;
	private $label;
	
	

	public function __construct($name, $label, $size, $value = ""){
		$this->name = $name;
		$this->label = $label;
		$this->size = $size;
		$this->value = $value;
	}
	
	
	/**
	 * retourne le html correspondant à l'élément
	 * @return string html
	 */
	public function toHTML(){
		$html = "<div id='{$this->name}Box'>";
		$html .= "<label for='$this->name'>$this->label</label>";
		$html .= "<input type='text' name='$this->name' id='$this->name' value='$this->value'/>";
		$html .= "</div>";
		
		return $html;
	}
}
/*****************************************************************************
 * End of file InputText.class.php
 */