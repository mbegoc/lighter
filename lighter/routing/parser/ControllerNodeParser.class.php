<?php
namespace lighter\routing\parser;

class ControllerNodeParser extends RouteParser {


    public function handleNode(Node $node, array $uri){
        echo "Controller:".$this->value;
        if($value = current($uri)){
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

