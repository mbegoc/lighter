<?php
namespace lighter\views;


use lighter\helpers\html\HtmlHeader;


class Example extends WebPage {


    /**
     * default constructor
     *
     * @param string $template
     */
    public function __construct() {
        parent::__construct('main', 'example');
        HtmlHeader::getInstance()->addCssFile('include/css/example.css');
    }

}

