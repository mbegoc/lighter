<?php
namespace lighter\views;


use lighter\helpers\html\HtmlHeader;

use \SimpleXMLElement;


/**
 * a specialized view to display http messages
 *
 * @name HttpMessage
 * @package lighter
 * @subpackage views
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class HttpMessage extends WebPage {
    /**
     * http response code
     *
     * @var int
     */
    protected $code;
    /**
     * http response message
     *
     * @var string
     */
    protected $message;


    /**
     * construct a view with the given code and message
     * this reset html header to be able to display a html format if necessary
     *
     * @param int $code
     * @param string $message
     */
    public function __construct($code, $message) {
        parent::__construct('main', 'httpMessage');

        $this->code = $code;
        $this->message = $message;

        $htmlHeader = HtmlHeader::getInstance();
        $htmlHeader->setTitle("$code - $message");
        $htmlHeader->resetJsFiles();
        $htmlHeader->resetCssFiles();

        $this->addMimeType('application/json', 'displayJson');
        $this->addMimeType('text/xml', 'displayXml');
    }


    /**
     * return the http code for template
     *
     * @return int
     */
    public function getCode() {
        return $this->code;
    }


    /**
     * return the message for template
     *
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }


    /**
     * display a json message
     */
    public function displayJson() {
        echo json_encode(array('code' => $this->code, 'message' => $this->message));
    }


    /**
     * Display an xml message
     */
    public function displayXml() {
        $xml = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><httpResponse></httpResponse>");
        $xml->addChild('code', $this->code);
        $xml->addChild('message', $this->message);

        echo $xml->asXml();
    }

}

