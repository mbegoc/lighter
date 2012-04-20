<?php
namespace lighter\routing\parser;


class MethodNodeParser extends RouteParser {


    public function handleNode(Node $node, array $uri) {
        if ($value = current($uri)) {
            $this->routeManager->setMethod($value);
            next($uri);
            $this->routeManager->handleNode($node, $uri);
        } else {
            $this->routeManager->setMethod($node->getValue());
            $this->routeManager->handleNode($node, $uri);
        }
        return true;
    }

}

