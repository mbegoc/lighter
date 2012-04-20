<?php
namespace lighter\routing\parser;


class ParamNodeParser extends RouteParser {


    public function handleNode(Node $node, array $uri) {
        if ($value = current($uri)) {
            $this->routeManager->addParam($value);
            next($uri);
            $this->routeManager->handleNode($node, $uri);
        }
        return true;
    }

}

