<?php
namespace lighter\views;


use lighter\helpers\html\HtmlHeader;


class StaticFile extends View {
    private $file;


    /**
     * default constructor
     *
     * @param string $template
     */
    public function __construct() {
        parent::__construct(null, null);
    }


    public function setFile($file) {
        $this->file = $file;
    }


    public function displayOther() {
        echo file_get_contents($this->file);
        return true;
    }


    public function displayHtml() {
        return false;
    }


    public function addMessage($message, $class = null) {
        throw new ViewException('This class can\'t handle messages', 100);
    }


    public function getMessages() {
        throw new ViewException('This class can\'t handle messages', 100);
    }

}

