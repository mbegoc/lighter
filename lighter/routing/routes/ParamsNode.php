<?php
namespace lighter\routing\routes;


class ParamsNode extends Node {


    public function __construct(){
        parent::__construct();
    }


    public function getType(){
        return 'ParamsNode';
    }

}

