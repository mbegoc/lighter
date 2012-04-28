<?php
namespace lighter\routing\parser;

class ControllerNodeParser extends RouteParser {


    public function handleNode(Node $node, array &$uri) {
        $value = current($uri);
        if ($value) {
            $this->routeManager->setController($value);
            next($uri);
            $this->routeManager->handleNode($node, $uri);
        }else{
            $this->routeManager->setController($node->getValue());
            $this->routeManager->handleNode($node, $uri);
        }
        return true;
    }

}

