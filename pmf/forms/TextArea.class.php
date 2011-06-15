<?php
/*****************************************************************************
 * Copyright (c) 2010 Michel Begoc
 * Author: Michel Begoc
 * Date: 2010-06-16
 * Description: 
 * 
 *****************************************************************************/
require_once("forms/FormElement.class.php");


class TextArea extends FormElement {
	private $size;
	private $label;
	
	
	/**
	 * @param string $name le nom de l'élément, servant à l'identifier
	 * @param string $label le nom "friendly user" de l'élément, à afficher dans le formulaire
	 * @param int $size le nombre de caractères maximum qui pourront être saisis dans ce champ
	 * @param string $value la valeur initiale du champ
	 */
	public function __construct($name, $label, $size, $value = ""){
		$this->name = $name;
		$this->label = $label;
		$this->size = $size;
		$this->value = $value;
	}
	
	
	/**
	 * @return string html
	 */
	public function toHTML(){
		$html = "<div id='{$this->name}Box'>";
		$html .= "<label for='$this->name'>$this->label</label>";
		$html .= "<textarea name='$this->name' id='$this->name'>$this->value</textarea>";
		$html .= "</div>";
		
		return $html;
	}
}
/*****************************************************************************
 * End of file TextArea.class.php
 */