<?php
namespace lighter\routing\routes;


class ParamNode extends Node {


    public function __construct(){
        parent::__construct();
    }


    public function getType(){
        return 'ParamNode';
    }

}

