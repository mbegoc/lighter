<?php
namespace pmf\helpers\html\tables;


/**
 * This table add to the basic table the ability to add columns containing
 * tools for editing purpose for example.
 *
 * @name EditTable
 * @package pmf
 * @subpackage helpers\html\tables
 * @see pmf\helpers\html\tables\Table
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class EditTable extends Table {
    /**
     * the label for the tools column
     *
     * @var string
     */
	public $toolsLabel = "Edit";
    /**
     * the list of tools
     *
     * @var array
     */
	public $tools = array();


    /**
     * default contructor
     */
	public function __construct($id){
		parent::__construct($id);
	}


	/**
     * set the label of the column which will contain the tools
	 *
	 * @param string $label
	 */
	public function setToolsLabel($label){
		$this->toolsLabel = (string)$label;
	}


	/**
     * add a tool to the tool column
	 *
	 * @param string $label
	 * @param string $url
	 */
	public function addTool($label, $url){
		$this->tools[] = array("url" => $url, "label" => $label);
	}


	/**
     * this method need to be redefined from the base class
	 *
	 * @param DataAccessor $da
	 */
	public function addRow(DataAccessor $da){
		if($this->highlight){
			$class = " class='highlighted'";
		}else{
			$class = "";
		}
		$this->highlight = !$this->highlight;

		$this->tbody.= "<tr$class>";
		foreach($this->cols as $col){
		    //FIXME I don't like this way of doing this, see how it could be done
		    $value = $da->{'get'.$col}();
			if(isset($value)){
				$this->tbody.= "<td$colspan>$value</td>";
			}else{
				$this->tbody.= "<td></td>";
			}
		}
		foreach($this->tools as $tool){
			$this->tbody.= "<td><a href='".$tool["url"].$da->getId()."'>".$tool["label"]."</a></td>";
		}
		$this->tbody.= "</tr>";
	}


	/**
     * convert this object into HTML
	 *
	 * @return string HTML
	 */
	public function toHTML(){
		$this->addHeaderCell($this->toolsLabel, count($this->tools));
		return parent::toHTML();
	}


    /**
     * convert this object into string - HTML serialization
     *
     * @see pmf\helpers\html\tables\EditTable::toHTML()
     * @return string HTML
     */
    public function __toString(){
        return $this->toHTML();
    }

}


