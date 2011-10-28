<?php
namespace pmf\controllers;


use pmf\handlers\Debug;

use pmf\dto\Menu;
use pmf\dto\Config;

use pmf\views\Admin as View;


/**
 * Admin controller class.
 *
 * @name Admin
 * @package pmf
 * @subpackage controllers
 * @see pmf\controllers\Controller
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
     * @see pmf\controllers.Controller::handleRequest()
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


    /**
     * handle the config form
     */
    public function config(){
        $config = Config::getInstance();

        $this->view = new View('detail');
        $this->view->setConfig($config);

        if($this->view->isUpdated()){
            $config->save($config);
            $this->view->addMessage(View::SAVE_OK);
        }
    }

}

