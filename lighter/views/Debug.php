<?php
namespace lighter\views;


use lighter\helpers\html\HtmlHeader;


class Debug extends WebPage {


    /**
     * default constructor
     *
     * @param string $template
     */
    public function __construct() {
        parent::__construct('main', 'debug/frameReport');
        $this->htmlHeader->addJsFile('include/js/debug.js');
        $this->htmlHeader->addCssFile('include/css/debug.css');
    }


    public function setMessages(array $messages) {
        $this->tplEngine->addVar('messages', $messages);
    }


    public function dumpToFile($file) {
        $this->tplEngine->addVar('time', date('d-m-Y H:i:s'));
        $this->tplEngine->addVar('htmlHeader', $this->htmlHeader);
        parent::dumpToFile($file);
    }

}

