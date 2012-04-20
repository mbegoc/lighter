<?php
namespace lighter\handlers;


use lighter\views\HttpMessage;
use lighter\views\View;


/**
 * represent a HttpResponse, so handle the http response header AND the http response
 * body, i.e. the webpage content.
 *
 * This class has been writed in regard of the HTTP RFC 2616.
 * See http://www.w3.org/Protocols/rfc2616/rfc2616.html
 *
 * @name HttpResponse
 * @package lighter
 * @subpackage handlers
 * @see lighter\handlers\HttpRequest
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class HttpResponse {
    /**
     * the single instance<
     *
     * @var handlers\HttpReponse
     */
    private static $instance = NULL;
    /**
     * the body of the HTTP response. It's a view which will produce it.
     *
     * @var views\View
     */
    private $body = NULL;
    /**
     * default HTTP messages, as described  by RFC 2616
     *
     * @var array
     */
    private $httpMessages;
    /**
     * the HTTP code which need a HTTP body, as described by RFC 2616
     *
     * @var unknown_type
     */
    private $httpBodyCodes;
    /**
     * The code of this response
     *
     * @var int - default 200
     */
    private $code = 200;
    /**
     * the content type of the response
     *
     * @var string - default text/html
     */
    private $contentType = 'text/html';


    /**
     * construct a new HttpResponse
     */
    private function __construct() {
        $this->httpMessages = array(
            100 => 'Continue',
            101 => 'Switching Protocols',

            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No content',
            205 => 'Reset Content',
            206 => 'Partial Content',

            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',

            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Excpectation Failed',

            500 => 'Internal server error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        );

        $this->httpBodyCodes = array(100, 101, 200,  201, 202, 203, 206);

    }


    /**
     * manage the singleton
     *
     * @return handlers\HttpResponse
     */
    public function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * set the http code for this response
     *
     * @param int $code
     * @throws HttpException
     */
    public function setCode($code) {
        if (isset($this->httpMessages[$code])) {
            $this->code = (int)$code;
        }else{
            throw new HttpException(_('Invalid HTTP code'));
        }
    }


    /**
     * return the http code of this response
     *
     * @return int
     */
    public function getCode() {
        return $this->code;
    }


    /**
     * set the body of the response, wich is generated by a View
     *
     * @param views\View $view
     */
    public function setBody(View $view) {
        $this->body = $view;
    }


    /**
     * send this response, i.e. write it to the output stream
     */
    public function send() {
        $this->contentType = HttpRequest::getInstance()->getAccept();
        $this->prepareHeader();
        if (in_array($this->code, $this->httpBodyCodes)) {
            switch ($this->contentType) {
                /* FIXME I don't like so much the way it works to know if a format is
                 * supported: the display method return a boolean in addition of
                 * displaying the content. I don't know how do that in a better way,
                 * and it works like that
                 */
                case 'text/html':
                /* I don't really know if we should handle the application/xhtml type,
                 * since my browser pop up to open the document in an application and
                 * doesn't display the xhtml page
                 */
//                case 'application/xhtml':
                    if ($this->body->displayHtml()) {
                        return;
                    }
                    break;
                case 'text/xml':
                    if ($this->body->displayXml()) {
                        return;
                    }
                    break;
                case 'application/json':
                    if ($this->body->displayJson()) {
                        return;
                    }
                    break;
            }
            $this->code = 406;
            $this->prepareHeader();
        }

        /* if we couldn't display the resource in an expected format, or if the http
         * code doesn't need a body generated by application then we display the http
         * code and message in a format as close as possible of the accept parameter
         * the views\HttpMessage class handle the body content
         */
        $httpMessage = new HttpMessage($this->code, $this->httpMessages[$this->code]);

        switch (HttpRequest::getInstance()->getAccept()) {
            case 'application/json':
                $httpMessage->displayJson();
                break;
            case 'text/xml':
                $httpMessage->displayXml();
                break;
            default:
                $this->contentType = 'text/html';
                $this->prepareHeader();
            case 'text/html':
            case 'application/xhtml':
                $httpMessage->displayHtml();
        }
    }


    /**
     * prepare the http header
     */
    private function prepareHeader() {
        $string = 'HTTP/1.1 '.$this->code.' '.$this->httpMessages[$this->code];
        header($string);
        header('Content-Type: '.$this->contentType);
    }

}

