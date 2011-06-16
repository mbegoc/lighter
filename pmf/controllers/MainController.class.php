<?php
namespace controllers;


use helpers\Exception;
use helpers\Debug;

use dto\Config;
use dto\DBAccessor;

use helpers\HttpResponse;


/**
 *
 * this class permit to route the request on the good (Sub)Controller, regarding the asked uri
 * it can be extended to alter the way the url is treated. If you do, the index.php will have to be updated too.
 * @author michel
 *
 */
class MainController {
    /**
     * base path of the uri, i.e. used if the application isn't at the root uri
     * @var string
     */
    protected $basePath;
    /**
     * the controller class corresponding to the asked uri
     * @var string
     */
    protected $controllerClass;
    /**
     * the method to call from the controller
     * @var string
     */
    protected $method = "handleRequest";
    /**
     * the arguments
     * @var array
     */
    protected $args = array();
    /**
     * the controller itself, i.e. its instance
     * @var Controller
     */
    protected $controller;
    /**
     * the main view, i.e. the presentation object for a basic webpage
     * @var MainView
     */
    protected $mainView;
    /**
     * the debug object
     * @var Debug
     */
    protected $debug;
    /**
     * the config object
     * @var Config
     */
    protected $config;


    /**
     * set the object regarding the requested uri
     */
    public function __construct(){
        $this->debug = Debug::getInstance('mainController');
        $this->debug->startProfiling('Page generation start');

        Exception::convertErrorToException();

        $this->config = Config::getInstance();
        $this->basePath = $this->config->getApplicationRelativePath();

        $this->controllerClass = $this->config->getControllerClass();

        $matches = array();
        if(preg_match("#$this->basePath(\w+.php/)?((/?\w+)+)/?$#", $_SERVER["REQUEST_URI"], $matches)){
            $this->debug->dump($matches);

            $this->args = explode('/', $matches[2]);

            $dba = new DBAccessor("menu");

            $firstArg = array_shift($this->args);
            $dba->search(array("short" => $firstArg));
            $menu = $dba->next();
            if(isset($menu)){
                $this->controllerClass = $menu->getController();
            }else{
                $this->controllerClass = $firstArg;

                $method = array_shift($this->args);
                if(isset($method)){
                    $this->method = $method;
                }
            }
        }
    }


    /**
     * execution of the controller
     */
    public function execute(){
        $this->debug->profilingCP('Sub controller call');

        $response = HttpResponse::getInstance();

        if(file_exists("pmf/controllers/$this->controllerClass.class.php")){
            $controller = '\\controllers\\'.$this->controllerClass;
            $this->controller = new $controller();

            $mainViewClass = '\\views\\'.$this->config->getMainViewName();
            $this->mainView = new $mainViewClass();

            if(method_exists($this->controller, $this->method)){
                call_user_func_array(array($this->controller, $this->method), $this->args);

                //we have to call the main set the subView in the mainView and then display the content
                $this->mainView->setSubView($this->controller->getView());
                //it's the RestResponse object which handle the return
                $response->setBody($this->mainView);
            }else{
                $response->setCode(404);
            }
        }else{
            $response->setCode(404);
        }

        $this->debug->profilingCP('Start of page rendering');

        $response->send();

        $this->debug->endProfiling('Page generation end');
    }

}

