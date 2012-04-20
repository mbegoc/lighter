<?php
namespace lighter\helpers\html\forms;


use lighter\handlers\Debug;


/**
 * an html input checkbox
 *
 * @name InputCheckBox
 * @package lighter
 * @subpackage helpers\html\forms
 * @see lighter\helpers\html\forms\Input
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class InputCheckBox extends Input {


    /**
     * construct a html checkbox
     *
     * @param string $name
     * @param string $label
     * @param string $value
     */
    public function __construct($name, $label, $value = false) {
        parent::__construct('checkbox', FormElement::BOOL, $name, $label, 0, $value);
    }


    /**
     * produce a html string
     *
     * @return string html
     */
    public function __toString() {
        if ($this->value) {
            $checked = " checked='checked'";
        }
        $html = "<div id='{$this->name}Box'>";
        $html.= "<input type='$this->inputType' name='$this->name' id='$this->name' value='1'$checked/>";
        $html.= "&nbsp;<label for='$this->name'>$this->label</label>";
        $html.= "</div>";

        return $html;
    }

}


