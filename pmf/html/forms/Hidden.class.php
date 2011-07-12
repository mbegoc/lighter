<?php
namespace html\forms;


/**
 * a hidden input element
 * @author michel
 *
 */
class Hidden extends Input {


    /**
     *
     * @param string $name
     * @param mixed $value
     */
    public function __construct($name, $type, $value = ""){
        parent::__construct('hidden', $type, $name, NULL, $size, $value);
	}


	/**
	 * return a html string
	 * @return string
	 */
	public function __toString(){
		return "<input type='$this->type' name='$this->name' id='$this->name' value='$this->value' size='$this->size'/>";
	}
}

