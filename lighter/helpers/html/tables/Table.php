<?php
namespace lighter\helpers\html\tables;


/**
 * Abstraction class for the HTML tables
 *
 * @name Table
 * @package lighter
 * @subpackage helpers\html\tables
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class Table {
    /**
     * the columns labels of the table
     *
     * @var array
     */
    protected $cols = array();
    /**
     * the table HTML tag
     * 
     * @var string
     */
    protected $table = "";
    /**
     * the table HTML tbody tag
     *
     * @var string
     */
    protected $tbody = "<tbody>";
    /**
     * the table HTML thead tag
     *
     * @var string
     */
    protected $thead = NULL;
    /**
     * the table HTML tfoot tag
     *
     * @var string
     */
    protected $tfoot = NULL;
    /**
     * a flag used for highlighting alternance of lines
     *
     * @var boolean
     */
    protected $highlight = false;


    /**
     * default constructor - initiate the table HTML tag
     */
    public function __construct($id) {
        $this->table = "<table id=$id>";
    }


    /**
     * add a column to the table
     *
     * @param string $name
     */
    public function addCol($name) {
        $this->cols[] = $name;
    }


    /**
     * add a line to the table.
     * Data is retrieve from a DataAccessor object
     *
     * @param DataAccessor $da
     * @todo this method need to be fix: it has been writed for another kind of
     * of objects than DataAccessor and we need to fix the way it accesses the
     * data
     */
    public function addRow(DataAccessor $da) {
        if ($this->highlight) {
            $class = " class='highlighted'";
        }else{
            $class = "";
        }
        $this->highlight = !$this->highlight;

        $this->tbody.= "<tr$class>";
        foreach ($this->cols as $col) {
            $value = $da->{'get'.$col}();
            if ($value) {
                $this->tbody.= "<td$colspan>$value</td>";
            }else{
                $this->tbody.= "<td></td>";
            }
        }
        $this->tbody.= "</tr>";
    }


    /**
     * add a cell to the header of the table
     * the programmer has to ensure the right number of cells are added
     *
     * @param string $cell
     * @param int $span
     */
    public function addHeaderCell($cell, $span = 1) {
        if ($this->thead == NULL) {
            $this->thead = "<thead><tr>";
        }
        if ($span > 1) {
            $colspan = " colspan='$span'";
        }
        $this->thead.= "<td$colspan>$cell</td>";
    }


    /**
     * add a cell to the footer of the table
     * the programmer has to ensure the right number of cells are added
     *
     * @param string $cell
     * @param int $span
     */
    public function addFooterCell($cell, $span = 1) {
        if ($this->tfoot == NULL) {
            $this->tfoot = "<tfoot><tr>";
        }
        if ($span > 1) {
            $colspan = " colspan='$span'";
        }
        $this->tfoot.= "<td$colspan>$cell</td>";
    }


    /**
     * convert this object into HTML
     *
     * @return string HTML
     */
    public function toHTML() {
        if ($this->thead != NULL) {
            $this->thead.= "</tr></thead>";
        }
        if ($this->tfoot != NULL) {
            $this->tfoot.= "</tr></tfoot>";
        }
        return    $this->table.
                $this->thead.
                $this->tfoot.
                $this->tbody."</tbody>".
                "</table>";
    }
    

    /**
     * convert this object into string - HTML serialization
     *
     * @see lighter\helpers\html\tables\EditTable::toHTML()
     * @return string HTML
     */
    public function __toString() {
        return $this->toHTML();
    }

}


