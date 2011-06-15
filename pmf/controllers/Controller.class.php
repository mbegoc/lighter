<?php
namespace controllers;

use views\MainView;


abstract class Controller {
    public abstract function handleRequest();

    public abstract function getView();
}