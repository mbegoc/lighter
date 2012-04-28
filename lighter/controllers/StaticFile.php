<?php
namespace lighter\controllers;


use lighter\handlers\HttpRequest;

use lighter\handlers\HttpResponse;

use lighter\views\HttpMessage;

use lighter\views\StaticFile as View;


/**
 * Admin controller class.
 *
 * @name Admin
 * @package lighter
 * @subpackage controllers
 * @see lighter\controllers\Controller
 * @since 0.1
 * @version 0.1
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


    public function returnResource($file) {
        if (is_file(LIGHTER_PATH.$file)) {
            $this->view = new View();
            $this->view->setFile(LIGHTER_PATH.$file);
        } else {
            HttpResponse::getInstance()->setCode(404);
        }
    }

}

