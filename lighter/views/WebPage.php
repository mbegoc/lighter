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
    protected $title = '';
    protected $contentTemplate = null;
    protected $htmlHeader = null;


    public function __construct($mainTemplate, $contentTemplate) {
        parent::__construct($mainTemplate);
        $this->contentTemplate = $contentTemplate;
        $this->htmlHeader = new HtmlHeader();
    }


    protected function displayHtml() {
        $this->tplEngine->addVar("htmlHeader", $this->htmlHeader);
        parent::displayHtml();
    }


    /**
     * return the HTML actual content of the page
     *
     * @return string
     */
    public function getMainContent() {
        return $this->tplEngine->get($this->contentTemplate);
    }


    public function setTitle($title) {
        $this->title = $title;
    }


    public function getTitle() {
        return $this->title;
    }


    public function getHtmlHeader() {
        return $this->htmlHeader;
    }

}
