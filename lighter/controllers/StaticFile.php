<?php
namespace lighter\controllers;


use lighter\handlers\Config;

use lighter\handlers\HttpRequest;
use lighter\handlers\HttpResponse;

use lighter\views\HttpMessage;
use lighter\views\StaticFile as View;


/**
 * A simple controller which grant access to the static files of the
 * framework that are not present into the application tree.
 * In production environment, static files of the framwork should be
 * copied to the application tree to avoid the use of this controller.
 *
 * @name Admin
 * @package lighter
 * @subpackage controllers
 * @see lighter\controllers\Controller
 * @since 0.1
 * @version 0.1.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class StaticFile extends Controller {


    /**
     * default action
     *
     * @see lighter\controllers.Controller::handleRequest()
     */
    public function handleRequest() {
        HttpResponse::getInstance()->setCode(404);
    }


    /**
     * retreive the file if it exists and init the view that will return
     * it to the client.
     *
     * @param string $file
     */
    public function returnResource($file) {
        $resource = Config::getInstance()->completePath($file);
        if (is_file($resource)) {
            $this->view = new View();
            $this->view->setFile($resource);
        } else {
            HttpResponse::getInstance()->setCode(404);
        }
    }

}

