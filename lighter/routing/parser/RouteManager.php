<?php
namespace lighter\routing\parser;


class RouteManager extends RouteParser {
    private $parsers = array();
    private $controller = NULL;
    private $method = NULL;
    private $params = array();


    public function __construct() {
        $this->parsers['FixedNode'] = new FixedNodeParser($this);
        $this->parsers['ControllerNode'] = new ControllerNodeParser($this);
        $this->parsers['MethodNode'] = new MethodNodeParser($this);
        $this->parsers['ParamNode'] = new ParamNodeParser($this);
        $this->parsers['ParamsNode'] = new ParamsNodeParser($this);
        $this->parsers['StaticFileNode'] = new StaticFileNodeParser($this);
    }


    public function setController($controller) {
        $this->controller = $controller;
    }


    public function getController() {
        return $this->controller;
    }


    public function setMethod($method) {
        $this->method = $method;
    }


    public function getMethod() {
        return $this->method;
    }


    public function addParam($param) {
        $this->params[] = $param;
    }


    public function getParams() {
        return $this->params;
    }


    public function handleNode(Node $node, array &$uri) {
        if ($subNode = $node->nextNode()) {
            if (!$this->parsers[$subNode->getType()]->handleNode($subNode, $uri)) {
                $this->handleNode($node, $uri);
            }
        }
    }


    public function __toString() {
        $s = $this->controller.':'.$this->method;
        foreach ($this->params as $param) {
            $s.= ':'.$param;
        }
        return $s;
    }

}

