<?php
/*****************************************************************************
 * Copyright (c) 2010 Michel Begoc
 * Author: michel
 * Date: 2010-06-16
 * Description: englobe les champ input password html
 * 
 *****************************************************************************/
require_once("forms/FormElement.class.php");


class Password extends FormElement {
	private $size;
	private $label;
	
	public function __construct($name, $label, $size){
		$this->name = $name;
		$this->size = $size;
		$this->label = $label;
	}
	
	public function toHTML(){
		$html = "<div id='{$this->name}Box'>";
		$html.= "<label for='$this->name'>$this->label</label>";
		$html.= "<input type='password' name='$this->name' id='$this->name'/>";
		$html.= "</div>";
		
		return $html;
	}
}
/*****************************************************************************
 * End of file Password.class.php
 */