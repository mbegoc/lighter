<?php
namespace lighter\routing\parser;


use lighter\handlers\Debug;

class StaticFileNodeParser extends RouteParser {


    /**
     *
     * @see lighter\routing\parser.RouteParser::handleNode()
     */
    public function handleNode(Node $node, array &$uri) {
        $value = current($uri);
        if ($value && $value == $node->getValue()) {
            $this->routeManager->setController('StaticFile');
            $this->routeManager->setMethod('returnResource');
            $this->routeManager->addParam(implode('/', $uri));
            return true;
        } else {
            return false;
        }
    }

}

