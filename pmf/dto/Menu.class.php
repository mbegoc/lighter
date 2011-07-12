<?php
namespace dto;


class Menu extends DataObject {

    public function __construct(array $doc = NULL){
        if($doc != NULL){
            parent::__construct($doc);
        }

        //FIXME this should be got from config
        $this->doc['controller'] = 'Content';
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

    public function setControllerMethod($controller){
        $this->doc['method'] = $controller;
    }

    public function getControllerMethod(){
        return $this->doc['method'];
    }

    public function publish(){
        $this->doc['published'] = true;
    }

    public function unpublish(){
        $this->doc['published'] = true;
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

    public function prepareToDB(){
        parent::prepareToDB(__CLASS__);
    }
}