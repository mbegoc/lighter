<?php
namespace html\forms;


use handlers\HttpRequest;

use \Exception;


/**
 * Permet de gérer des formulaires HTML.
 * La classe représente un formulaire HTML. On va pouvoir ensuite
 * lui demander soit de fournir le formulaire HTML, soit d'analyser
 * le formulaire qui sera posté et de le transformer en VoObjet.
 * Edit: l'idée de VoObjet va probablement être compliqueé à mettre
 * en oeuvre (à moins peut-être de passer un objet vide en paramètre
 * à la fonction et de boucler dans la liste de champs)
 *
 * @author Michel Begoc
 * @copyright Michel Begoc (c) 2010
 *
 */
class Form {
    /**
     * the name of the form
     * @var string
     */
	protected $name;
	/**
	 * the target of the form
	 * @var string
	 */
	protected $target;
	/**
	 * the method to send the form through
	 * @var string
	 */
	protected $method;
	/**
	 * the list of fields of the form
	 * @var array
	 */
	protected $elements;
	/**
	 * fieldset title
	 * @var string
	 */
	protected $fieldset = NULL;


	/**
	 * create a new form
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
	 * @param FormElement $element
	 */
	public function addElement(FormElement $element){
		$this->elements[] = $element;
	}


	/**
	 *
	 * @param string $label
	 */
	public function setFieldSet($label){
		$this->fieldset = $label;
	}


	/**
	 * return this form in html format
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
	 * retourne les valeurs saisies pour ce formulaire
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
	 * @return boolean
	 */
	public function isPosted(){
	    $submit = HttpRequest::getInstance()->getString($this->name.'Submit');
		return isset($submit);
	}


	/**
	 * name getter
	 * @return string
	 */
	public function getName(){
	    return $this->name;
	}
}


class FormException extends Exception {}

