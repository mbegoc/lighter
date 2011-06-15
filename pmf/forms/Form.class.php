<?php
/*****************************************************************************
 * Copyright (c) 2010 Michel Begoc
 * Author: Michel Begoc
 * Date: 2010-06-16
 *
 *****************************************************************************/
require_once("forms/InputText.class.php");
require_once("forms/Hidden.class.php");
require_once("forms/Password.class.php");
require_once("forms/TextArea.class.php");


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
	private $name;
	private $target;
	private $method;
	private $elements;

	private $fieldset = NULL;


	/**
	 * crée un nouveau formulaire dont la cible est $target
	 * @param $target
	 */
	public function __construct($name, $target = "", $method = "post"){
		$this->name = $name;
		$this->target = $target;
		$this->method = $method;

		$this->elements = array();
	}


	/**
	 * ajoute un nouvel élément à ce formulaire
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
	 * retourne le formulaire au format html
	 */
	public function toHTML(){
		$html = "<div id='{$this->name}Box'>";

		if(isset($this->fieldset)){
			$html.= "<fieldset><legend>$this->fieldset</legend>";
		}

		$html.= "<form name='$this->name' id='$this->name' method='$this->method' action='$this->target'>";
		foreach($this->elements as $element){
			$html.= $element->toHTML();
		}
		$html.= "<div id='{$this->name}ButtonBox'>";
		$html.= "<input type='submit' name='$this->name[submit]' value='Envoyer'/>";
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
	 * complète un vo avec les valeurs du formulaire
	 * @param VoBase $vo
	 */
	public function fillVo(&$vo){
		foreach($this->elements as $element){
			$vo->{$element->getName()} = $element->getValue();
		}
	}


	/**
	 * détermine si ce formulaire a été complété ou non
	 * @return boolean
	 */
	public function isPosted(){
		return isset($_POST[$this->name]["submit"]);
	}
}
/*****************************************************************************
 * End of file FormHelper.class.php
 */