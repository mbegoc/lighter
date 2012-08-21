<?php
namespace lighter\handlers;


use lighter\helpers\html\HtmlHeader;

use lighter\handlers\Config;
use lighter\handlers\TemplateEngine;

use lighter\views\Debug as View;

use \SimpleXMLElement;
use \Exception;


/**
 * This class aims to facilitate debuggage. It picks up debbuging messages to
 * display them in a readable way in a convenient format for the programmer.

 * @name Debug
 * @package lighter
 * @subpackage handlers
 * @since 0.1
 * @version 0.1.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class Debug {
    /*
     * STATIC ATTRIBUTES - GLOBAL DEBUG CONFIGURATION
    */
    /**
     * the name of the debug class to use
     * @staticvar string
     */
    private static $debugClass = 'lighter\handlers\Debug';
    /**
     * the messages list
     * @staticvar array
     */
    private static $messages = array();
    /**
     * the instances list of the debugger
     * @staticvar array
     */
    private static $instances = array();
    /**
     * the configuration object
     * @var lighter\handlers\Config
     */
    private static $config;

    /*
     * INSTANCE ATTRIBUTES
    */
    /**
     * the name of the debug section
     * @var string
     */
    private $curSection;
    /**
     * an index for auto generated names
     * @var int
     */
    private $i;
    /**
     * a time field for profiling purpose
     * @var float
     */
    private $time = null;


    /**
     * a private constructor for the singleton
     * @param string $section
     */
    private function __construct($section) {
        $this->curSection = $section;
        self::$messages[$section] = array();
        $this->i = array("message" => 1, "var" => 1, "exception" => 1, "trace" => 1, "profiling" => 1);
    }


    /**
     * this is a multiton
     *
     * @static
     * @param string $section
     * @return Debug
     */
    public static function getInstance($section = "default") {
        if (isset(self::$instances[$section])) {
            return self::$instances[$section];
        }else{
            if (count(self::$instances) == 0) {
                self::$config = Config::getInstance();
                if (!self::$config->getValue('debug', 'active', false)) {
                    self::$debugClass = 'lighter\handlers\FakeDebug';
                } else {
                    self::$debugClass = get_class();
                }
            }

            self::$instances[$section] = new self::$debugClass($section);
            return self::$instances[$section];
        }
    }


    /**
     * create the reports
     */
    public function __destruct() {
        if (count(self::$instances) == 1) {
            if (self::$config->getValue('debug', 'report', false)) {
                if (count(self::$messages) > 0) {
                    $this->generateReport();
                }
            }
        }

        unset(self::$instances[$this->curSection]);
    }


    /**
     * add a message
     *
     * @param string $type
     * @param string $title
     * @param string $content
     * @param array $location
     */
    private function addMessage($type, $title, $content, array $location = null) {
        if (is_null($location)) {
            $trace = debug_backtrace();
            //$trace[0] est l'appel interne à cette méthode par une methode publique de l'objet
            $location = array("file" => $trace[1]["file"], "line" => $trace[1]["line"]);
        }
        self::$messages[$this->curSection][] = array("type" => $type, "title" => $title, "content" => $content, "file" => $location["file"], "line" => $location["line"]);
    }


    /**
     * log a debug message
     *
     * @param string $message
     * @param string $title
     */
    public function log($message, $title = null) {
        if (is_null($title)) {
            $title = "Message ".$this->i["message"]++;
        }
        $this->addMessage("Message", $title, $message);
    }


    /**
     * dump a variable
     *
     * @param mixed $variable
     * @param string $title
     */
    public function dump($variable, $title = null) {
        if (is_null($title)) {
            $title = "Variable ".$this->i["var"]++;
        }
        $this->addMessage("Variable", $title, "<pre>".print_r($variable, true)."</pre>");
    }


    /**
     * add a trace stack
     *
     * @param string $title
     */
    public function trace($title = null) {
        if (is_null($title)) {
            $title = "Trace ".$this->i["trace"]++;
        }
        $backtrace = debug_backtrace();
        foreach ($backtrace as $trace) {
            if ($trace["class"] !== "Debug") {
                $this->addMessage(
                	"Trace",
                $title,
                $trace["class"].$trace["type"].$trace["function"],
                array("file" => $trace["file"], "line" => $trace["line"])
                );
            }
        }
    }


    /**
     * start a profiling session
     *
     * @param string $title
     */
    public function startProfiling($title = null) {
        if ($title == null) {
            $title = "Start profiling";
        }
        $this->i["profiling"] = 1;
        $this->addMessage("Profiling", $title, "0 s");
        $this->time = microtime(true);
    }


    /**
     * create a checkpoint in a profiling session
     *
     * @param string $title
     */
    public function profilingCP($title = null) {
        $endTime = microtime(true);
        if ($title == null) {
            $title = $title = "Profiling CheckPoint".$this->i["profiling"]++;
        }

        $this->addMessage("Profiling", $title, ($endTime - $this->time)." s");
    }


    /**
     * end a profiling session
     *
     * @param string $title
     */
    public function endProfiling($title = null) {
        $endTime = microtime(true);
        if ($title == null) {
            $title = $title = "End profiling";
        }

        $this->addMessage("Profiling", $title, ($endTime - $this->time)." s");
    }


    /*
     * DISPLAY METHOD
    */
    /**
     * return well formatted messages
     *
     * @param array $sections
     */
    public function getMessages($sections = null) {
        if ($sections === null) {
            return self::$messages;
        } else {
            return array_intersect_key(self::$messages, $sections);
        }
    }


    /**
     * prepare a debug view
     *
     * @param lighter\helpers\html\HtmlHeader $htmlHeader
     * @param boolean $framed
     * @return lighter\views\View
     */
    public function prepareView(HtmlHeader $htmlHeader) {
        $htmlHeader->addCssFile('include/css/debug.css');
        if (self::$config->getValue('debug', 'redirect', false) && isset($_SERVER['HTTP_HOST'])) {
            $htmlHeader->jsRedirect('http://'.$_SERVER['HTTP_HOST'].'/'
                .self::$config->getValue('debug', 'reportFile', false));
        }
        $view = new View();
        $view->setMessages($this->getMessages(
            self::$config->getValue('debug', 'sections', null)));
        return $view;
    }


    /**
     * generate a report for the provided sections
     *
     * @param array $sections
     */
    public function generateReport() {
        if (isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'] !== '') {
            $path = $_SERVER['DOCUMENT_ROOT'].'/'
                .self::$config->getValue('debug', 'reportFile', false);
        }else{
            $path = self::$config->getValue('debug', 'scriptPath', false)
                .self::$config->getValue('debug', 'reportFile', false);
        }
        $view = new View();
        $view->setMessages($this->getMessages(
            self::$config->getValue('debug', 'sections', null)));

        $view->dumpToFile($path);
    }

}


/**
 * a fake debug class to use instead of the actual debug class in production
 * Its purpose is to minimize the number of "ifs" done on the debug flag. Instead of
 * check the flag, we use this empty class which does nothing in place of the true
 * debug class.
 *
 * @name FakeDebug
 * @package lighter
 * @subpackage handlers
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class FakeDebug extends Debug {
    public function __construct($section) {}
    public function __destruct() {}
    public function log($message, $title = null) {}
    public function dump($variable, $title = null) {}
    public function trace($title = null) {}
    public function startProfiling($title = null) {}
    public function profilingCP($title = null) {}
    public function endProfiling($title = null) {}
    public function getMessages($sections = null) {}
    public function prepareView(HtmlHeader $htmlHeader) { return null; }
    public function generateReport(View $view) {}
}

