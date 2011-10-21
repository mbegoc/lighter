<?php
/*****************************************************************************
 * Copyright (c) 2010 Michel Begoc
 * Author: Michel Begoc
 * Date: 2010-07-17
 * Description: Il s'agit d'une table qui a la particularité de proposer en plus
 * 				des tables habituelles la possibilité d'ajouter des colonnes
 * 				contenant des outils pour chaque ligne.
 *
 *****************************************************************************/
namespace html\tables;


class EditTable extends Table {
	public $toolsLabel = "Edit";
	public $tools = NULL;


	public function __construct($id){
		parent::__construct($id);
	}


	/**
	 * permet de spécifier le nom de la colonne qui va contenir les outils
	 * @param string $label
	 */
	public function setToolsLabel($label){
		$this->toolsLabel = (string)$label;
	}


	/**
	 * ajoute un nouvel outil
	 * @param string $label
	 * @param string $url
	 */
	public function setTool($label, $url){
		$this->tools[] = array("url" => $url, "label" => $label);
	}


	/**
	 * réécriture de la fonction de Table, adaptée aux besoins de la nouvelle classe
	 * @param DataObject $dto
	 */
	public function addRow(DataObject $dto){
		if($this->highlight){
			$class = " class='highlighted'";
		}else{
			$class = "";
		}
		$this->highlight = !$this->highlight;

		$this->tbody.= "<tr$class>";
		foreach($this->cols as $col){
		    $value = $dto->{'get'.$col}();
			if(isset($value)){
				$this->tbody.= "<td$colspan>$value</td>";
			}else{
				$this->tbody.= "<td></td>";
			}
		}
		foreach($this->tools as $tool){
			$this->tbody.= "<td><a href='".$tool["url"].$dto->getId()."'>".$tool["label"]."</a></td>";
		}
		$this->tbody.= "</tr>";
	}


	/**
	 * convertit cette table en HTML
	 * @return string HTML
	 */
	public function toHTML(){
		$this->addHeaderCell($this->toolsLabel, count($this->tools));
		return parent::toHTML();
	}
}

