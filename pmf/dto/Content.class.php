<?php
namespace dto;


class Content extends DataObject {


    public function __construct(array $doc = NULL){
        if($doc != NULL){
            parent::__construct($doc);
        }
    }

    public function setTitle($title){
        $this->doc['title'] = $title;
    }

    public function getTitle(){
        return $this->doc['title'];
    }

    public function setContent($Content){
        $this->doc['Content'] = $Content;
    }

    public function getContent(){
        return $this->doc['Content'];
    }

    public function prepareToDB(){
        parent::prepareToDB();
    }


    /**
     * (non-PHPdoc)
     * @see dto.DataObject::getClassName()
     */
    protected function getClassName(){
        return __CLASS__;
    }

}

