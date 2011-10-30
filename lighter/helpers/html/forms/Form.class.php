<?php
namespace lighter\helpers\html\forms;


use lighter\handlers\HttpRequest;

use \Exception;


/**
 * Handle HTML forms. The class represent a HTML form. We can ask it the
 * HTML form or to handle the posted data.
 *
 * @name Form
 * @package lighter
 * @subpackage helpers\html\forms
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class Form {
    /**
     * the name of the form
     *
     * @var string
     */
	protected $name;
	/**
	 * the target of the form
     *
	 * @var string
	 */
	protected $target;
	/**
	 * the method to send the form through
     *
	 * @var string
	 */
	protected $method;
	/**
	 * the list of fields of the form
     *
	 * @var array
	 */
	protected $elements;
	/**
	 * fieldset title
     *
	 * @var string
	 */
	protected $fieldset = NULL;


	/**
	 * create a new form
     *
	 * @param $target
	 */
	public function __construct($name, $target = "", $method = "post"){
		$this->name = $name;
		$this->target = $target;
		$this->method = $method;

		$this->elements = array();
	}


	/**
	 * add an element to the form
     *
	 * @param FormElement $element
	 */
	public function addElement(FormElement $element){
		$this->elements[] = $element;
	}


	/**
	 * set fieldset name
     *
	 * @param string $label
	 */
	public function setFieldSet($label){
		$this->fieldset = $label;
	}


	/**
	 * return this form in html format
     * @return string
	 */
	public function __toString(){
		$html = "<div id='{$this->name}Box' class='formBox'>";

		if(isset($this->fieldset)){
			$html.= "<fieldset><legend>$this->fieldset</legend>";
		}

		$html.= "<form name='$this->name' id='$this->name' method='$this->method' action='$this->target'>";
		foreach($this->elements as $element){
			$html.= $element;
		}
		$html.= "<div id='{$this->name}ButtonBox'>";
		$html.= "<input type='submit' name='{$this->name}Submit' value='Envoyer'/>";
		$html.= "</div>";

		$html.= "</form>";

		if(isset($this->fieldset)){
			$html.= "</fieldset>";
		}

		$html.= "</div>";

		return $html;
	}


	/**
	 * return the input values
     *
     * @return array
	 */
	public function getValues(){
		$return = array();
		foreach($this->elements as $element){
			$return[$element->getName()] = $element->getValue();
		}

		return $return;
	}


	/**
	 * say if this form have been posted or not
     *
	 * @return boolean
	 */
	public function isPosted(){
	    $submit = HttpRequest::getInstance()->getString($this->name.'Submit');
		return isset($submit);
	}


	/**
	 * name getter
     *
	 * @return string
	 */
	public function getName(){
	    return $this->name;
	}

}

/**
 * the exception thrown by the Form class
 *
 * @name FormException
 * @package lighter
 * @subpackage helpers\html\forms
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class FormException extends Exception {}


