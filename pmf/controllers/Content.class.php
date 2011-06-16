<?php
namespace controllers;

use dto\Content as Data;
use dto\DBAccessor;

use handlers\HtmlHeader;

use views\Content as View;


class Content extends Controller {

    protected $view;


    public function __construct(){
    }


    public function handleRequest(){
        $dba = new DBAccessor('content');
        $content = $dba->get('4de1b9443a0759c046000000');

        $this->view = new View();
        $this->view->setData($content);
        $this->view->addMessage(View::MESSAGE);
    }


    public function test($title = 'Test', $text = 'Test'){
        $content = new Data();
        $content->setTitle($title);
        $content->setContent($text);
        $this->view = new View();
        $this->view->setData($content);
        $this->view->addMessage(View::MESSAGE);
    }


    public function getView(){
        return $this->view;
    }
}