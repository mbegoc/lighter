<?php
namespace lighter\handlers;


use lighter\helpers\Exception;

use lighter\models\Config;
use lighter\models\ConfigException;

use lighter\handlers\Debug;
use lighter\handlers\HttpResponse;

use lighter\routing\parser\RouteManager;


/**
 *
 * this class permit to route the request on the good (Sub)Controller, regarding the
 * asked uri it can be extended to alter the way the url is treated. If you do, the
 * index.php will have to be updated too.
 *
 * @name Router
 * @package lighter
 * @subpackage handlers
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class Router {
    /**
     * base path of the uri, i.e. used if the application isn't at the root uri
     * @var string
     */
    protected $basePath;
    /**
     * the controller class corresponding to the asked uri
     *
     * @var string
     */
    protected $controllerClass;
    /**
     * the method to call from the controller
     *
     * @var string
     */
    protected $method;
    /**
     * the arguments
     *
     * @var array
     */
    protected $args = array();
    /**
     * the controller itself, i.e. its instance
     *
     * @var Controller
     */
    protected $controller;
    /**
     * the main view, i.e. the presentation object for a basic webpage
     *
     * @var MainView
     */
    protected $view;
    /**
     * the profile object
     *
     * @var profile
     */
    protected $profile;
    /**
     * the config object
     *
     * @var Config
     */
    protected $config;


    /**
     * set the object regarding the requested uri
     */
    public function __construct(){
        error_reporting(E_ALL);

        $this->profile = Debug::getInstance('Page profiling');
        $this->profile->startProfiling('Page generation start');

        Exception::convertErrorToException();

        $this->config = Config::getInstance();
        $this->basePath = $this->config->getApplicationRelativePath();

        $urlReport = Debug::getInstance('Url Data');
        $urlReport->startProfiling('Route handling');
        $matches = array();
        if(preg_match("#$this->basePath(\w+\.php/)?(.*)/?$#",
            $_SERVER["REQUEST_URI"], $matches))
        {
            $routeManager = new RouteManager();
            $routeManager->handleNode($this->config->getRoutes(), explode('/', $matches[2]));

            $this->controllerClass = $routeManager->getController();
            $this->method = $routeManager->getMethod();
            $this->args = $routeManager->getParams();
        }

        $urlReport->endProfiling('Route handling');
        $urlReport->log($this->controllerClass, 'controller');
        $urlReport->log($this->method, 'method');
        $urlReport->dump($this->args, 'arguments');
    }


    /**
     * execution of the controller
     */
    public function execute(){
        $this->profile->profilingCP('Initialization begining');

        $response = HttpResponse::getInstance();

        $paths = $this->config->getControllersPaths();
        foreach($paths as $path => $package){
            if(file_exists($path.$this->controllerClass.'.class.php')){
                $controller = $package.$this->controllerClass;
                $this->controller = new $controller();

                if(method_exists($this->controller, $this->method)){
                    call_user_func_array(
                        array($this->controller, $this->method),
                        $this->args
                    );

                    $this->view = $this->controller->getView();
                    $response->setCode(200);
                    $response->setBody($this->view);

                    break;
                }else{
                    $response->setCode(404);
                }
            }else{
                $response->setCode(404);
            }
        }

        $this->profile->profilingCP('Intialized - Start of page rendering');

        $response->send();

        $this->profile->endProfiling('Page generation end');
    }

}

