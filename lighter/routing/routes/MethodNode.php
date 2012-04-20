<?php
namespace lighter\routing\routes;


class MethodNode extends Node {


    public function __construct($default) {
        parent::__construct();
        $this->setValue($default);
    }


    public function getType() {
        return 'MethodNode';
    }

}

