<?php
namespace lighter\views;


use lighter\helpers\html\HtmlHeader;

use finfo;


class StaticFile extends View {
    private $file;
    private $finfo;


    /**
     * default constructor
     *
     * @param string $template
     */
    public function __construct() {
        parent::__construct(null, null);
        $this->resetMimeTypes();
        $this->finfo = new finfo();
    }


    public function setFile($file) {
        $this->file = $file;
        $this->defaultType = $this->finfo->file($this->file, FILEINFO_MIME_TYPE);
        $this->addMimeType($this->defaultType, 'displayFile');
    }


    public function displayFile() {
        echo file_get_contents($this->file);
    }


    public function addMessage($message, $class = null) {
        throw new ViewException('This class can\'t handle messages', 100);
    }


    public function getMessages() {
        throw new ViewException('This class can\'t handle messages', 100);
    }

}

