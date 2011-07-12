<?php
namespace html\forms;


/**
 * an html input checkbox
 * @author michel
 *
 */
use handlers\Debug;

class InputCheckBox extends Input {


    /**
     * construct a html checkbox
     * @param string $name
     * @param string $label
     * @param string $value
     */
    public function __construct($name, $label, $value = false){
        parent::__construct('checkbox', FormElement::BOOL, $name, $label, 0, $value);
    }


	/**
	 * produce a html string
	 * @return string html
	 */
	public function __toString(){
	    if($this->value){
	        $checked = " checked='checked'";
	    }
		$html = "<div id='{$this->name}Box'>";
		$html.= "<input type='$this->inputType' name='$this->name' id='$this->name' value='1'$checked/>";
		$html.= "&nbsp;<label for='$this->name'>$this->label</label>";
		$html.= "</div>";

		return $html;
	}

}

