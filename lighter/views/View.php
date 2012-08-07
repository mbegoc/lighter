<?php
namespace lighter\views;


use lighter\handlers\HttpResponse;
use lighter\handlers\TemplateEngine;

use lighter\helpers\html\HtmlHeader;

use \Exception;


/**
 * The very basic View class. Instantiate the TemplateEngine and display a content,
 * regarding the providing template.
 *
 * @name View
 * @abstract
 * @package lighter
 * @subpackage views
 * @since 0.1.0
 * @version 0.1.0
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
abstract class View {
    /**
     * the template engine
     * @var lighter\handlers\TemplateEngine
     */
    protected $tplEngine;
    /**
     * the template to display
     * @var string
     */
    protected $mainTemplate;
    /**
     * a messages list to display
     * @var string
     */
    protected $messages = "";
    /**
     * an array containing the supported mime types and the associated function to
     * call.
     * @var array
     */
    protected $supportedMimeTypes = array();
    protected $defaultType = null;


    /**
     * constructor
     *
     * @param string $template
     */
    public function __construct($mainTemplate) {
        $this->mainTemplate = $mainTemplate;

        $this->tplEngine = new TemplateEngine();

        $this->addMimeType('text/html', 'displayHtml');
        $this->setDefaultMimeType('text/html');
    }


    /**
     * display the main template of the page
     *
     * @param string $type - the mime type of the displayed content
     */
    public function display($type) {
        $call = $this->supportedMimeTypes[$type];
        $this->{$call}();
    }


    public function dumpToFile($file) {
        $this->tplEngine->addVar("view", $this);
        $html = $this->tplEngine->get($this->mainTemplate);
        file_put_contents($file, $this->tplEngine->get($this->mainTemplate));
    }


    public function isSupportedMimeType($type) {
        return isset($this->supportedMimeTypes[$type]);
    }


    public function setDefaultMimeType($type) {
        $this->defaultType = $type;
    }


    public function getDefaultMimeType() {
        return $this->defaultType;
    }


    protected function addMimeType($type, $call) {
        $this->supportedMimeTypes[$type] = $call;
    }


    protected function resetMimeTypes() {
        $this->supportedMimeTypes = array();
    }


    protected function displayHtml() {
        $this->tplEngine->addVar("view", $this);
        $this->tplEngine->display($this->mainTemplate);
    }


    /**
     * add a message to the page
     *
     * @param string $message
     * @param string $class
     */
    public function addMessage($message, $class = NULL) {
        if (isset($class)) {
            $class = " class='$class'";
        }
        $this->messages.= "<p$class>$message</p>";
    }


    /**
     * return the messages
     *
     * @return string
     */
    public function getMessages() {
        return $this->messages;
    }

}


/**
 * The exception thrown by the Views classes
 *
 * @name ViewException
 * @package lighter
 * @subpackage views
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class ViewException extends Exception {}


