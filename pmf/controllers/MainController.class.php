<?php
namespace controllers;

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
     * @var views\MainView
     */
    protected $mainView;


    /**
     * set the object regarding the requested uri
     */
    public function __construct(){
        $config = Config::getInstance();
        $this->basePath = $config->getApplicationRelativePath();

        $this->controllerClass = $config->getControllerClass();

        $matches = array();
        if(preg_match("#$this->basePath(\w+.php/)?((/?\w+)+)#", $_SERVER["REQUEST_URI"], $matches)){
            $this->args = explode('/', $matches[2]);

            $dba = new DBAccessor("menu");

            $firstArg = array_shift($this->args);
            $dba->search(array("short" => $firstArg));
            $menu = $dba->next();
            if(isset($menu)){
                $this->controllerClass = $menu->getController();
            }else{
                $this->controllerClass = $firstArg;
                //TODO we have to test if this controller file exists. If not, we should return a 404 HTTP code
                $method = array_shift($this->args);
                if(isset($method)){
                    $this->method = $method;
                }
            }
        }

        $mainViewClass = '\\views\\'.$config->getMainViewName();
        $this->mainView = new $mainViewClass();


        //FIXME \\controllers path should also be in the config
        $controller = '\\controllers\\'.$this->controllerClass;
        $this->controller = new $controller();
    }


    /**
     * execution of the controller
     */
    public function execute(){
        //TODO si le controlleur ou la méthode n'existent pas, il faut renvoyer une 404 à la place
        call_user_func_array(array($this->controller, $this->method), $this->args);

        //we have to call the main set the subView in the mainView and then display the content
        $this->mainView->setSubView($this->controller->getView());

        //it's the RestResponse object which handle the return
        $response = HttpResponse::getInstance();
        $response->setBody($this->mainView);
        $response->send();
    }

}

