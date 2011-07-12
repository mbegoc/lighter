<?php
namespace html\forms;


/**
 * an input text
 * @author michel
 *
 */
abstract class Input extends FormElement {
    /**
     * the field type
     * @var string
     */
    protected $inputType;
    /**
     * the size of the field
     * @var int
     */
	protected $size;
	/**
	 * the label (title) of the field
	 * @var string
	 */
	protected $label;
	/**
	 * the field name
	 * @var string
	 */
	protected $name;
	/**
	 * the field value
	 * @var string
	 */
	protected $value;


	/**
	 *
	 * @param string $name
	 * @param string $label
	 * @param int $size
	 * @param mixed $value
	 */
	public function __construct($inputType, $type, $name, $label, $size, $value){
	    parent::__construct($type);
	    $this->inputType = $inputType;
		$this->name = $name;
		$this->label = $label;
		$this->size = $size;
		$this->value = $value;
	}


	/**
	 * produce a html string
	 * @return string html
	 */
	public function __toString(){
		$html = "<div id='{$this->name}Box'>";
		$html.= "<label for='$this->name'>$this->label: </label>";
		$html.= "<input type='$this->inputType' name='$this->name' id='$this->name' value='$this->value'/>";
		$html.= "</div>";

		return $html;
	}

}

