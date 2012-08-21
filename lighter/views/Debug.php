<?php
namespace lighter\views;


use lighter\helpers\html\HtmlHeader;


/**
 * A Debug view to generate Debug reports (as a independant page or
 * a frame embed in the original page)
 *
 * @name Debug
 * @package lighter
 * @subpackage views\Debug
 * @see lighter\views\WebPage
 * @since 0.1
 * @version 0.1.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class Debug extends WebPage {
    protected $framed = true;


    /**
     * default constructor
     *
     * @param boolean $framed
     */
    public function __construct() {
        parent::__construct('main', 'debug/frameReport');
        $this->htmlHeader->addCssFile('include/css/debug.css');
        $this->tplEngine->addVar('origin', $_SERVER['REQUEST_URI']);
    }


    /**
     * intialize the view with the debug messages list to display
     *
     * @param array $messages
     */
    public function setMessages(array $messages) {
        $this->tplEngine->addVar('time', date('d-m-Y H:i:s'));
        $this->tplEngine->addVar('messages', $messages);
    }


    /**
     * @see lighter\views.WebPage::getMainContent()
     */
    public function getMainContent() {
        if ($this->framed) {
            $this->tplEngine->addVar('classes', 'framed');
        }
        return parent::getMainContent();
    }


    /**
     * @see lighter\views.View::dumpToFile()
     */
    public function dumpToFile($file) {
        $this->framed = false;
        $this->tplEngine->addVar('htmlHeader', $this->htmlHeader);
        parent::dumpToFile($file);
        $this->framed = true;
    }

}

