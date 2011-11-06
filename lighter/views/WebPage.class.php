<?php
namespace lighter\views;


use lighter\handlers\HttpResponse;
use lighter\handlers\TemplateEngine;

use lighter\helpers\html\HtmlHeader;

use \Exception;


/**
 * The very basic View class. Instantiate the TemplateEngine and display a content,
 * regarding the providing template.
 *
 * @author michel
 *
 */
abstract class WebPage extends View {
    protected $title = "";
    protected $contentTemplate = NULL;


    public function __construct($mainTemplate, $contentTemplate){
        parent::__construct($mainTemplate);
        $this->contentTemplate = $contentTemplate;
    }


    public function displayHtml(){
        self::$tplEngine->addObject("htmlHeader", HtmlHeader::getInstance());
        parent::displayHtml();
        return true;
    }


    /**
     * return the HTML actual content of the page
     *
     * @return string
     */
    public function getMainContent(){
        return self::$tplEngine->get($this->contentTemplate);
    }


    public function setTitle($title){
        $this->title = $title;
    }


    public function getTitle(){
        return $this->title;
    }

}
