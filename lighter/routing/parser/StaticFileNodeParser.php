<?php
namespace lighter\routing\parser;


class StaticFileNodeParser extends RouteParser {


    public function handleNode(Node $node, array $uri) {
        if (($value = current($uri)) && $value == $node->getValue()) {
            $this->routeManager->setController('StaticFile');
            $this->routeManager->addParam(implode('/', $uri));
        } else {
            $this->routeManager->handleNode($node, $uri);
        }
        return true;
    }

}

