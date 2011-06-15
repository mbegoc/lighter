<?php
namespace helpers;


use views\View;

class HttpResponse {
    private static $instance = NULL;
    /**
     *
     * Enter description here ...
     * @var View
     */
    private $body = NULL;
    private $code = 200;
    private $httpMessages;
    private $httpBodyCodes;


    private function __construct(){
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

    public function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new self();
        }

        return self::$instance;
    }


    public function setHttpResponseCode($code){
        $this->code = (int)$code;
    }


    public function getHttpResponseCode(){
        return $this->code;
    }


    public function setBody(View $view){
        $this->body = $view;
    }

    public function send(){
        $this->prepareHeader();

        //FIXME here if the code is an error, we should return a body containing the http error code and message
        switch(HttpRequest::getInstance()->getAccept()){
            case 'text/html':
            case 'application/xhtml':
                $this->body->display();
                break;
            case 'text/xml':
                //FIXME these try / catch are... dirty ?
                try{
                    $this->body->displayXml();
                }catch(Exception $e){}
                    break;
            case 'application/json':
                try{
                    $this->body->displayJson();
                }catch(Exception $e){}
                break;
            default:
                $this->code = 406;//pour l'exemple
                $this->prepareHeader();
        }
    }

    public function prepareHeader(){
        $string = 'HTTP/1.1 '.$this->code.' '.$this->httpMessages[$this->code];
        header($string);
    }

}

