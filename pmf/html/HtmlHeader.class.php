<?php
namespace html;


class HtmlHeader {
    private static $instance;

    protected $jsFiles = "";
    protected $cssFiles = "";
    protected $title = "";
    protected $canonical = "";
    protected $charset = "";
    protected $description = "";
    protected $keywords = "";


    private function __construct(){

    }

    public function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function setTitle($title){
        $this->title = "<title>$title</title>";
    }

    public function setCanonicalTag($url){
        $this->canonical = "";
    }

    public function setCharset($charset){
        $this->charset = "<meta http-equiv='Content-Type' content='text/html;charset=$charset'/>";
    }

    public function setDescription($description){
        $this->description = "<meta name='description' content='$description'/>";
    }

    public function setKeywords($keywords){
        $this->keywords = "<meta name='keywords' content='$keywords'/>";
    }

    public function addCssFile($file, $media = 'all'){
        $this->cssFiles.= "<link type='text/css' rel='stylesheet' href='$file' media='$media'/>";
    }

    public function addJsFile($file, $type = 'text/javascript'){
        $this->jsFiles.= "<script type='$type' src='$file'></script>";
    }


    public function resetCssFiles(){
        $this->cssFiles = '';
    }


    public function resetJsFiles(){
        $this->jsFiles = '';
    }


    public function display(){
        echo '<head>';
        echo $this->charset;
        echo $this->title;
        echo $this->canonical;
        echo $this->cssFiles;
        echo $this->jsFiles;
        echo $this->description;
        echo $this->keywords;
        echo '</head>';
    }
}

