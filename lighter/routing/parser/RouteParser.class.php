<?php
namespace lighter\routing\parser;


/**
 *
 *
 * @name
 * @package
 * @subpackage
 * @see
 * @since
 * @version
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
abstract class RouteParser {
    /**
     * the route manager which will collect controller execution information
     *
     * @var lighter\routing\parser\RouteManager
     */
    protected $routeManager = NULL;


    /**
     * initialize this object with the route manager to use
     *
     * @param RouteManager $routeManager
     */
    public function __construct(RouteManager $routeManager){
        $this->routeManager = $routeManager;
    }


    /**
     * the method which will be called to handle a node
     *
     * @param Node $node
     * @param array $uri
     */
    public abstract function handleNode(Node $node, array $uri);

}

