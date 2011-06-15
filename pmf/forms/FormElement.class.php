<?php
/*****************************************************************************
 * Copyright (c) 2010 Michel Begoc
 * Author: Michel Begoc
 * Date: 2010-06-16
 * Description: l'interface que chaque élément de formulaire doit implanter.
 * 
 *****************************************************************************/
require_once("helpers/Debug.class.php");

abstract class FormElement {
	const STRING = 1;
	const INT = 2;
	const FLOAT = 3;
	const BOOL = 4;
	
	private $type = self::STRING;
	protected $name = "";
	protected $value;
	
	
	/**
	 * cette fonction va générer le html permettant l'affichage de cet élément
	 */
	public abstract function toHTML();
	
	
	/**
	 * retourne la valeur de l'élément, castée en fonction du type
	 * @return mixed
	 */
	public function getValue(){
		switch($this->type){
			case self::STRING:
				return $this->getString();
				break;
			case self::INT:
				return $this->getInt();
				break;
			case self::FLOAT:
				return $this->getFloat();
				break;
			case self::BOOL:
				return $this->getBool();
				break;
			default:
				
		}
	}
	
	
	/**
	 * retourne la valeur de l'élément
	 * @return string
	 */
	private function getString(){
		if(isset($_POST[$this->name])){
			$string = trim($_POST[$this->name]);
			if($string != ""){
				return $string;
			}
		}
		return NULL;
	}
	
	
	/**
	 * convertit la valeur de l'élément en int
	 * @return int
	 */
	private function getInt(){
		if(isset($_POST[$this->name]) && trim($_POST[$this->name]) != ""){
			return (int)$_POST[$this->name];
		}else{
			return NULL;
		}
	}
		
	
	/**
	 * convertit la valeur de l'élément en float
	 * @return float
	 */
	private function getFloat(){
		if(isset($_POST[$this->name]) && trim($_POST[$this->name]) != ""){
			return (float)$_POST[$this->name];
		}else{
			return NULL;
		}
	}
		
	
	/**
	 * convertit la valeur du champ en boolean
	 * @return boolean
	 */
	private function getBool(){
		if(isset($_POST[$this->name]) && trim($_POST[$this->name]) != ""){
			return (bool)$_POST[$this->name];
		}else{
			return NULL;
		}
	}
	
	
	/**
	 * retourne le nom de l'élément de fromulaire
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}
}
/*****************************************************************************
 * End of file FormElement.class.php
 */