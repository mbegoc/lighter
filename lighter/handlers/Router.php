<?php
namespace lighter\handlers;


use lighter\helpers\Exception;

use lighter\handlers\Config;
use lighter\handlers\ConfigException;

use lighter\handlers\Logger;
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
     * @var Logger
     */
    protected $logger;
    /**
     * the config object
     *
     * @var Config
     */
    protected $config;


    /**
     * set the object regarding the requested uri
     */
    public function __construct() {
        error_reporting(E_ALL);

        $this->logger = Logger::getInstance('lighter');
        $this->logger->startProfiling('entire page');
        $this->logger->info('Starting of routing process');

        Exception::convertErrorToException();

        $this->config = Config::getInstance();

        $this->logger->info('Handling of URL');
        $this->logger->startProfiling('url handling');
        $matches = array();
        if (preg_match("#(\w+\.php/)?/(.*)/?$#", $_SERVER["REQUEST_URI"], $matches)) {
            $routeManager = new RouteManager();
            $routeManager->handleNode($this->config->getRoutes(), explode('/', $matches[2]));

            $this->controllerClass = $routeManager->getController();
            $this->method = $routeManager->getMethod();
            $this->args = $routeManager->getParams();
        }
        $this->logger->endProfiling('url handling');
    }


    /**
     * execution of the controller
     */
    public function execute() {
        $this->logger->info('Execute request');
        $response = HttpResponse::getInstance();

        $paths = $this->config->getSection('controllersPaths');
        $requestOk = false;
        foreach ($paths as $path => $package) {
            if (file_exists($path.$this->controllerClass.'.php')) {
                $controller = $package.$this->controllerClass;
                $this->controller = new $controller();

                if (method_exists($this->controller, $this->method)) {
                    call_user_func_array(
                        array($this->controller, $this->method),
                        $this->args
                    );

                    $this->view = $this->controller->getView();
                    $response->setBody($this->view);
                    $requestOk = true;
                    break;
                }
            }
        }

        if (!$requestOk) {
            $response->setCode(404);
        }

        $this->logger->info('Sending response');
        $response->send();

        $this->logger->endProfiling('entire page');
    }

}

