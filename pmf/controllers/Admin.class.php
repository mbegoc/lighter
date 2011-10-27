<?php
namespace controllers;


use handlers\Debug;

use dto\Menu;
use dto\Config;

use views\Admin as View;


class Admin extends Controller {


    public function handleRequest(){
        $this->panel();
    }


    public function panel(){
        $this->view = new View('panel');
    }


    public function menu($id = NULL){
        $menu = new Menu();
        if(isset($id)){
            if($id != 'emptyForm'){
                $menu->load($id);
            }
            $this->view = new View('detail');
            $this->view->setMenu($menu);

            if($this->view->isUpdated()){
                $menu->save();
                $this->view->addMessage(View::SAVE_OK);
            }
        }else{
            $menu->loadAll();
            $this->view = new View('list');
            $this->view->initMenuList($menu);
        }
    }



    public function config(){
        $config = Config::getInstance();

        $this->view = new View('detail');
        $this->view->setConfig($config);

        if($this->view->isUpdated()){
            $config->save($config);
            $this->view->addMessage(View::SAVE_OK);
        }
    }


    public function groups(){

    }


    public function users(){

    }

}