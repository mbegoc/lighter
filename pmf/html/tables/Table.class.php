<?php
/*****************************************************************************
 * Copyright (c) 2010 Michel Begoc
 * Author: michel
 * Date: 2010-07-10
 * Description: class d'abstraction php pour les tables HTML
 *
 *****************************************************************************/
namespace helpers;


class Table {
	protected $cols = array();
	protected $table = "";
	protected $tbody = "<tbody>";
	protected $thead = NULL;
	protected $tfoot = NULL;
	protected $highlight = false;

	public function __construct($id){
		$this->table = "<table id=$id>";
	}


	/**
	 * ajoute une colonne à la table
	 * @param string $name
	 */
	public function addCol($name){
		$this->cols[] = $name;
	}


	/**
	 * Ajoute une ligne à la table
	 * Les données sont passées par un VoObject
	 * @param VoObject $vo
	 */
	public function addRow(&$vo){
		if($this->highlight){
			$class = " class='highlighted'";
		}else{
			$class = "";
		}
		$this->highlight = !$this->highlight;

		$this->tbody.= "<tr$class>";
		foreach($this->cols as $col){
			if(isset($vo->{$col})){
				$this->tbody.= "<td$colspan>$col".$vo->{$col}."</td>";
			}else{
				$this->tbody.= "<td></td>";
			}
		}
		$this->tbody.= "</tr>";
	}


	/**
	 * ajoute une cellule à l'entête de la table
	 * Cette fonction ne créera pas les cellules manquantes si il en manque
	 * @param string $cell
	 * @param int $span
	 */
	public function addHeaderCell($cell, $span = 1){
		if($this->thead == NULL){
			$this->thead = "<thead><tr>";
		}
		if($span > 1){
			$colspan = " colspan='$span'";
		}
		$this->thead.= "<td$colspan>$cell</td>";
	}


	/**
	 * Ajoute une cellule au footer
	 * Cette fonction ne créera pas les cellules manquantes si il en manque
	 * @param string $cell
	 * @param int $span
	 */
	public function addFooterCell($cell, $span = 1){
		if($this->tfoot == NULL){
			$this->tfoot = "<tfoot><tr>";
		}
		if($span > 1){
			$colspan = " colspan='$span'";
		}
		$this->tfoot.= "<td$colspan>$cell</td>";
	}


	/**
	 * génére le HTML associé à cette table
	 */
	public function toHTML(){
		if($this->thead != NULL){
			$this->thead.= "</tr></thead>";
		}
		if($this->tfoot != NULL){
			$this->tfoot.= "</tr></tfoot>";
		}
		return	$this->table.
				$this->thead.
				$this->tfoot.
				$this->tbody."</tbody>".
				"</table>";
	}
}
/*****************************************************************************
 * End of file Table.class.php
 */