<?php
namespace lighter\routing\routes;


class FixedNode extends Node {


    public function __construct($value) {
        parent::__construct();
        $this->setValue($value);
    }


    public function getType() {
        return 'FixedNode';
    }

}

