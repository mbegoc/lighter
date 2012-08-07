<?php
namespace lighter\routing\parser;


use lighter\helpers\String;


class MethodNodeParser extends RouteParser {


    public function handleNode(Node $node, array &$uri) {
        $value = current($uri);
        if ($value) {
            $method = $value;
            next($uri);
        } else {
            $method = $node->getValue();
        }
        $this->routeManager->setMethod(String::camelize($method));
        $this->routeManager->handleNode($node, $uri);
        return true;
    }

}

