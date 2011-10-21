<?php
namespace controllers;


use dto\Menu;

use handlers\Debug;

use dto\DBAccessor;
use dto\Config;

use views\Admin as View;


class Admin extends Controller {


    public function handleRequest(){
        $this->pannel();
    }


    public function pannel(){
        $this->view = new View('pannel');
    }


    public function menu($id = NULL){
        $dba = new DBAccessor('menu');
        if(isset($id)){
            if($id == 'emptyForm'){
                $menu = new Menu();
            }else{
                $menu = $dba->get($id);
            }
            $this->view = new View('detail');
            $this->view->setMenu($menu);

            if($this->view->isUpdated()){
                $dba->save($menu);
                $this->view->addMessage(View::SAVE_OK);
            }
        }else{
            $dba->search();
            $this->view = new View('list');
            $this->view->initMenuList($dba);
        }
    }



    public function config(){
        $config = Config::getInstance();

        $this->view = new View('detail');
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