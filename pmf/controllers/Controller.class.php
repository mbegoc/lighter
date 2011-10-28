<?php
namespace pmf\controllers;


use pmf\views\View;


/**
 * The Controllers Base.
 *
 * This class defined the default method which can always be called.
 *
 * @name Controller
 * @abstract
 * @package pmf
 * @subpackage controllers
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
abstract class Controller {
    /**
     * the default view
     * @var pmf\views\View
     */
    protected $view;


    /**
     * the default action method which is offered by all the controllers
     *
     * @abstract
     */
    public abstract function handleRequest();


    /**
     * view getter
     * @return pmf\views\View
     */
    public function getView(){
        return $this->view;
    }

}

