<?php
namespace controllers;

use handlers\HttpRequest;

use dto\Content as Data;
use dto\DBAccessor;

use views\Content as View;


class Content extends Controller {


    public function __construct(){
        $this->view = new View();
    }


    public function handleRequest(){
        //FIXME we should take the default document from the config
        $this->show('4de1b9443a0759c046000000');
    }


    public function show($id){
        $dba = new DBAccessor('content');
        $content = $dba->get($id);

        switch(HttpRequest::getInstance()->getMethod()){
            case 'POST':
                $this->view->addMessage(View::SAVED);
                break;
            case 'DELETE':
            case 'PUT':
            case 'GET':
            default:
                break;
        }

        $this->view->setData($content);
    }


}