<?php
namespace lighter\helpers\html\forms;


/**
 * a text area field
 *
 * @name TextArea
 * @package lighter
 * @subpackage helpers\html\forms
 * @see lighter\helpers\html\forms\FormElement
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class TextArea extends FormElement {
    /**
     * the size of the field
     *
     * @var int
     */
	private $size;
	/**
	 * the label (title) of the field
     *
	 * @var string
	 */
	private $label;


	/**
     * defalt constructor
     *
	 * @param string $name le nom de l'élément, servant à l'identifier
	 * @param string $label le nom "friendly user" de l'élément, à afficher dans le formulaire
	 * @param int $size le nombre de caractères maximum qui pourront être saisis dans ce champ
	 * @param string $value la valeur initiale du champ
	 */
	public function __construct($name, $label, $size, $value = ""){
	    parent::__construct(FormElement::STRING);
		$this->name = $name;
		$this->label = $label;
		$this->size = $size;
		$this->value = $value;
	}


	/**
     * serialize this objeect into a HTML string
     *
	 * @return string html
	 */
	public function __toString(){
		$html = "<div id='{$this->name}Box'>";
		$html .= "<label for='$this->name'>$this->label</label>";
		$html .= "<textarea name='$this->name' id='$this->name'>$this->value</textarea>";
		$html .= "</div>";

		return $html;
	}

}


