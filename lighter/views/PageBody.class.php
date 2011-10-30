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
abstract class PageBody extends View {
    protected $title = "";


    public function __construct($mainTemplate, $contentTemplate){
        parent::__construct($mainTemplate, $contentTemplate);
    }


    public function setTitle($title){
        $this->title = $title;
    }


    public function getTitle(){
        return $this->title;
    }

}