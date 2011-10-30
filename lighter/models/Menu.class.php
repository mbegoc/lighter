<?php
namespace lighter\models;


use lighter\handlers\Debug;

class Menu extends DataAccessor {

    public function __construct(){
        parent::__construct('menu');
        $this->doc['controller'] = 'Content';
        $this->doc['method'] = 'handleRequest';
    }

    public function loadFromSlug($slug){
        $this->search(array("short" => $slug));
        return $this;
    }

    public function setTitle($title){
        $this->doc['title'] = $title;
    }

    public function getTitle(){
        return $this->doc['title'];
    }

    public function setShort($short){
        $this->doc['short'] = $short;
    }

    public function getShort(){
        return $this->doc['short'];
    }

    public function setController($controller){
        $this->doc['controller'] = $controller;
    }

    public function getController(){
        return $this->doc['controller'];
    }

    public function setControllerMethod($method){
        $this->doc['method'] = $method;
    }

    public function getControllerMethod(){
        return $this->doc['method'];
    }

    /**
     * id of the item to pass to the controller
     */
    public function setItemId($itemId){
        $this->doc['itemId'] = (string)$itemId;
    }
    public function getItemId(){
        return $this->doc['itemId'];
    }

    public function setPublished($published){
        $this->doc['published'] = $published;
    }

    public function isPublished(){
        return $this->doc['published'];
    }

    public function addSubMenu(Menu $menu){
        $this->doc['submenus'][] = $menu;
    }

    public function getSubMenus(){
        return $this->doc['submenus'];
    }

    public function removeSubMenu(Menu $menu){
        if($index = array_search($menu, $this->doc['submenus'])){
            unset($this->doc['submenus'][$index]);
        }
    }

    protected function prepareToDB(){

    }

    public function setValues(array $values, $prefix = ""){
        $this->setTitle($values[$prefix.'title']);
        $this->setShort($values[$prefix.'short']);
        $this->setController($values[$prefix.'controllerClass']);
        $this->setControllerMethod($values[$prefix.'controllerMethod']);
        $this->setItemId($values[$prefix.'itemId']);
        $this->setPublished($values[$prefix.'published']);
    }

}

