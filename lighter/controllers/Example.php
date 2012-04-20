<?php
namespace lighter\controllers;


use lighter\views\Example as View;


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
class Example extends Controller {


    /**
     * default action
     *
     * @see lighter\controllers.Controller::handleRequest()
     */
    public function handleRequest() {
        $this->view = new View();
    }

}

