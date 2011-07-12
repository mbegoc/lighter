<?php
namespace controllers;

use views\MainView;


abstract class Controller {
    protected $view;

    public abstract function handleRequest();

    public function getView(){
        return $this->view;
    }
}