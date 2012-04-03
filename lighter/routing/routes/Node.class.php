<?php
namespace lighter\routing\routes;


use \Serializable;


abstract class Node implements Serializable {
    private $type;
    private $value;
    private $subNodes = array();


    public function __construct(){

    }


    public function addSubNode(Node $node){
        $this->subNodes[] = $node;
        return $node;
    }


    public function nextNode(){
        $current = current($this->subNodes);
        next($this->subNodes);
        return $current;
    }


    abstract public function getType();


    public function setValue($value){
        $this->value = $value;
    }


    public function getValue(){
        return $this->value;
    }


    public function __toString(){
        $s = $this->type.':'.$this->value;
        $endLine = '';
        foreach($this->subNodes as $subNode){
            $s.= '->'.$subNode.$endLine;
            $endLine = "\n";
        }
        return $s;
    }


    public function reset(){
        foreach($this->subNodes as $subNode){
            $subNode->reset();
        }
        reset($this->subNodes);
    }


    public function serialize(){
        return json_encode(array($this->type, $this->value, serialize($this->subNodes)));
    }


    public function unserialize($s){
        list($this->type, $this->value, $this->subNodes) = json_decode($s);
        $this->subNodes = unserialize($this->subNodes);
    }

}

