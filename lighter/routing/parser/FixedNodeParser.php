<?php
namespace lighter\routing\parser;


class FixedNodeParser extends RouteParser {


    public function handleNode(Node $node, array $uri) {
        if ($value = current($uri)) {
            if ($node->getValue() == $value) {
                next($uri);
                $this->routeManager->handleNode($node, $uri);
            }else{
                return false;
            }
        }
        return true;
    }

}

