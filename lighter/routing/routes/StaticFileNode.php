<?php
namespace lighter\routing\routes;


class StaticFileNode extends Node {


    public function __construct($staticPath) {
        parent::__construct();
        $this->setValue($staticPath);
    }


    public function getType() {
        return 'StaticFileNode';
    }

}

