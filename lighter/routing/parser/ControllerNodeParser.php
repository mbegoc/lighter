<?php
namespace lighter\routing\parser;


use lighter\helpers\String;


class ControllerNodeParser extends RouteParser {


    public function handleNode(Node $node, array &$uri) {
        $value = current($uri);
        if ($value) {
            $controller = $value;
            next($uri);
        }else{
            $controller = $node->getValue();
        }
        $this->routeManager->setController(String::camelize($controller, true));
        $this->routeManager->handleNode($node, $uri);
        return true;
    }

}

