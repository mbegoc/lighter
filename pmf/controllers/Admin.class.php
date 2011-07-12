<?php
namespace controllers;


use handlers\Debug;

use dto\DBAccessor;
use dto\Config;

use views\Admin as View;


class Admin extends Controller {


    public function handleRequest(){
        $this->view = new View('pannel');
    }


    public function pannel(){
        $this->handleRequest();
    }


    public function menu(){
        $this->view = new View('menu');
    }



    public function config(){
        $config = Config::getInstance();

        $this->view = new View('config');
        $this->view->setConfig($config);

        if($this->view->isUpdated()){
            $dba = new DBAccessor('config');
            $dba->save($config);
            $this->view->addMessage(View::SAVE_OK);
        }
    }


    public function groups(){

    }


    public function users(){

    }

}