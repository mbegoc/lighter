<?php
namespace pmf\views;


use pmf\html\HtmlHeader;

use \SimpleXMLElement;


/**
 * a specialized view to display http messages
 * @author michel
 *
 */
class HttpMessage extends View {
    /**
     * http response code
     * @var int
     */
    protected $code;
    /**
     * http response message
     * @var string
     */
    protected $message;


    /**
     * construct a view with the given code and message
     * this reset html header to be able to display a html format if necessary
     * @param int $code
     * @param string $message
     */
    public function __construct($code, $message){
        parent::__construct('main', 'httpMessage');

        $this->code = $code;
        $this->message = $message;

        $htmlHeader = HtmlHeader::getInstance();
        $htmlHeader->setTitle("$code - $message");
        $htmlHeader->resetJsFiles();
        $htmlHeader->resetCssFiles();
    }


    /**
     * return the http code for template
     * @return int
     */
    public function getCode(){
        return $this->code;
    }


    /**
     * return the message for template
     * @return string
     */
    public function getMessage(){
        return $this->message;
    }


    /**
     * (non-PHPdoc)
     * @see views.View::displayJson()
     */
    public function displayJson(){
        echo json_encode(array('code' => $this->code, 'message' => $this->message));
    }


    /**
     * (non-PHPdoc)
     * @see views.View::displayXml()
     */
    public function displayXml(){
        $xml = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><httpResponse></httpResponse>");
        $xml->addChild('code', $this->code);
        $xml->addChild('message', $this->message);

        echo $xml->asXml();
    }

}

