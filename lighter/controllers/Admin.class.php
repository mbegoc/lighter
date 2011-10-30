<?php
namespace lighter\controllers;


use lighter\handlers\Debug;

use lighter\models\Menu;
use lighter\models\Config;

use lighter\views\Admin as View;


/**
 * Admin controller class.
 *
 * @name Admin
 * @package lighter
 * @subpackage controllers
 * @see lighter\controllers\Controller
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class Admin extends Controller {


    /**
     * default action
     *
     * @see lighter\controllers.Controller::handleRequest()
     */
    public function handleRequest(){
        $this->panel();
    }


    /**
     * display the main panel of admin page
     */
    public function panel(){
        $this->view = new View('panel');
    }


    /**
     * handle the routing path
     *
     * @param string $id
     */
    public function menu($id = NULL){
        $menu = new Menu();
        if(isset($id)){
            if($id != 'emptyForm'){
                $menu->load($id);
            }
            $this->view = new View('detail');
            $this->view->initMenu($menu);

            if($this->view->isDataUpdated()){
                $menu->setValues($this->view->getData());
                $menu->save();
                $this->view->addMessage(View::SAVE_OK);
            }
        }else{
            $menu->loadAll();
            $this->view = new View('list');
            $this->view->initMenuList($menu);
        }
    }


    /**
     * handle the config form
     */
    public function config(){
        $config = Config::getInstance();

        $this->view = new View('detail');
        $this->view->initConfig($config);

        if($this->view->isDataUpdated()){
            $config->setValues($this->view->getData());
            $config->save($config);
            $this->view->addMessage(View::SAVE_OK);
        }
    }

}

