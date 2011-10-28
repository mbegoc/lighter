<?php
namespace pmf\html\forms;


/**
 * a hidden input element
 *
 * @name Hidden
 * @package pmf
 * @subpackage helpers\html\forms
 * @see pmf\helpers\html\forms\Input
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class Hidden extends Input {


    /**
     * default constructor
     *
     * @param string $name
     * @param mixed $value
     */
    public function __construct($name, $type, $value = ""){
        parent::__construct('hidden', $type, $name, NULL, $size, $value);
	}


	/**
	 * return a html string
     *
	 * @return string
	 */
	public function __toString(){
		return "<input type='$this->type' name='$this->name' id='$this->name' value='$this->value' size='$this->size'/>";
	}

}


