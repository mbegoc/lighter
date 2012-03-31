<?php
namespace lighter\routing\parser;


abstract class RouteParser {
    protected $routeManager = NULL;

    public function __construct(RouteManager $routeManager){
        $this->routeManager = $routeManager;
    }

    public abstract function handleNode(Node $node, array $uri);

}

