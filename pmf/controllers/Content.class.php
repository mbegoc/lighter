<?php
namespace controllers;

use handlers\HtmlHeader;

use views\Content as View;
use dto\DBAccessor;


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


    public function getView(){
        return $this->view;
    }
}