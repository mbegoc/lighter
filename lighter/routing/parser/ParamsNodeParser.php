<?php
namespace lighter\routing\parser;


class ParamsNodeParser extends RouteParser {


    public function handleNode(Node $node, array &$uri) {
        if ($value = current($uri)) {
            $this->routeManager->addParam($value);
        }
        if (next($uri)) {
            $this->handleNode($node, $uri);
        } else {
            $this->routeManager->handleNode($node, $uri);
        }
        return true;
    }

}

