<?php
namespace views;


use helpers\HttpRequest;

use handlers\HtmlHeader;


/**
 * The MainView aims to display the whole content to respond to the request.
 * It may be HTML or other.
 * To make your own HTML template, you can simply modify the main.tpl.php file.
 * If you need many root templates, you can extends this class to handle your templates properly.
 * The config will have to be updated with the right class name.
 *
 * @author michel
 *
 */
class MainView extends View {
    /**
     * the sub view, i.e. the view object which will generated the actual content.
     * @var SubView
     */
    protected $subView;


    /**
     * call the parent constructor with the default main template name
     */
    public function __construct(){
        parent::__construct('main');
    }


    /**
     * the subView setter
     * @param SubView $subView
     */
    public function setSubView(SubView $subView){
        $this->subView = $subView;
    }


    /**
     * we redefine this function to handle properly the other content types than html
     * (non-PHPdoc)
     * @see views.View::display()
     */
    public function display(){
        self::$tplEngine->addObject("body", $this->subView);
        self::$tplEngine->addObject("htmlHeader", HtmlHeader::getInstance());
        parent::display();
    }

}

