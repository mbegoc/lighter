<?php
namespace views;

use dto\Content as Data;

use html\HtmlHeader;


/**
 * the View of Content controller. Its purpose is to display a simple webpage.
 * @author michel
 *
 */
class Content extends View {
    const SAVED = 'Modifications sauvegardée.';
    const MESSAGE = "Ceci est un message type, une erreur par exemple.";


    /**
     *
     */
    public function __construct(){
        parent::__construct('main', 'content');
    }


    /**
     * set the Data to display, i.e. a Content dto object
     * @param Data $data
     */
    public function setData(DataObject $data){
        self::$tplEngine->addObject("data", $data);

        HtmlHeader::getInstance()->setTitle($data->getTitle());
    }

}

