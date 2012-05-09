<?php
namespace lighter\handlers;


use lighter\handlers\Config;

use \Exception;


/**
 * This class aims to facilitate debuggage. It picks up debbuging messages to
 * display them in a readable way in a convenient format for the programmer.

 * @name Debug
 * @package lighter
 * @subpackage handlers
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class Logger {
    const TRACE   = 'TraceLogger',
          DUMP    = 'DumpLogger',
          PROFILE = 'ProfilerLogger',
          DEBUG   = 'DebugLogger',
          INFO    = 'InfoLogger',
          WARNING = 'WarningLogger',
          ERROR   = 'ErrorLogger',
          FATAL   = 'FatalLogger',
          NONE    = 'Logger';


    /**
     * the messages list
     * @var array
     */
    private $messages = array();
    /**
     * the instances list of the debugger
     * @staticvar array
     */
    private static $instances = array();
    /**
     * the configuration object
     * @var lighter\handlers\Config
     */
    private $config;

    /**
     * the name of the debug section
     * @var string
     */
    private $section;
    /**
     * a time field for profiling purpose
     * @var float
     */
    private $time = array();


    private $file = null;


    /**
     * a private constructor for the singleton
     * @param string $section
     */
    public function __construct($section) {
        $this->section = $section;
        $this->config = Config::getInstance();
    }


    /**
     * the principle of the singleton, but not an actual singleton
     *
     * @static
     * @param string $section
     * @return lighter\handlers\Logger
     */
    public static function getInstance($section = "default", $level = null) {
        if (!isset(self::$instances[$section])) {
            if ($level == null) {
                $levels = Config::getInstance()->getValue('log', 'levels', array());
                if (isset($levels[$section])) {
                    $level = $levels[$section];
                } else {
                    $level = Config::getInstance()->getValue('log', 'level', self::WARNING);
                }
            }
            $class = __NAMESPACE__.'\\'.$level;
            self::$instances[$section] = new $class($section);
        }
        return self::$instances[$section];
    }


    /**
     * create the reports
     */
    public function __destruct() {
        fclose($this->file);
    }


    /**
     * add a message
     *
     * @param string $type
     * @param string $title
     * @param string $content
     * @param array $location
     */
    protected function addTimedMessage($level, $content) {
        $this->addMessage('<'.date('Y-m-d h:m:s').'> '.$level.' : '.$content);
    }

    protected function addMessage($content) {
        if ($this->file === null) {
            $path = $this->config->getValue('log', 'path', '.').$this->section.'.log';
            $this->file = fopen($path, 'a');
        }
        fwrite($this->file, $content."\n");
    }


    /**
     * log a trace
     */
    public function trace() {}


    /**
     * log a dump of a variable
     *
     * @param mixed $variable
     */
    public function dump($variable) {}


    /**
     * log a debug message
     *
     * @param string $message
     */
    public function debug($message) {}


    /**
     * start a profiling operation
     *
     * @param string $label
     */
    public function startProfiling($label) {}


    /**
     * end a profiling operation started with startProfiling.
     * If the profiling operation has not been started, this method will throw a
     * NonexistentProfilingOperation Exception.
     *
     * @param string $label
     * @throw lighter\handlers\NonexistentProfilingOperation
     */
    public function endProfiling($label) {}


    /**
     * log an informationnal message
     *
     * @param string $message
     */
    public function info($message) {}


    /**
     * log a warning message
     *
     * @param string $message
     */
    public function warning($message) {}


    /**
     * log an error message
     *
     * @param string $message
     */
    public function error($message) {}


    /**
     * log a fatal error message
     *
     * @param unknown_type $message
     */
    public function fatal($message) {}

}


/**
 * a fake debug class to use instead of the actual debug class in production
 * Its purpose is to minimize the number of if done on the debug flag. Instead of
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
class FatalLogger extends Logger {


    public function __construct($section) {
        parent::__construct($section);
    }


    /**
     * @see lighter\handlers.Logger::fatal()
     */
    public function fatal($message) {
        $this->addTimedMessage('Fatal error', $message);
    }

}


class ErrorLogger extends FatalLogger {


    public function __construct($section) {
        parent::__construct($section);
    }


    /**
     * @see lighter\handlers.Logger::error()
     */
    public function error($message) {
        $this->addTimedMessage('Error', $message);
    }

}


class WarningLogger extends ErrorLogger {


    public function __construct($section) {
        parent::__construct($section);
    }


    /**
     * @see lighter\handlers.Logger::warn()
     */
    public function warning($message) {
        $this->addTimedMessage('Warning', $message);
    }

}


class InfoLogger extends WarningLogger {


    public function __construct($section) {
        parent::__construct($section);
    }


    /**
     * @see lighter\handlers.Logger::info()
     */
    public function info($message) {
        $this->addTimedMessage('Info', $message);
    }

}


class ProfilerLogger extends WarningLogger {


    public function __construct($section) {
        parent::__construct($section);
    }


    /**
     * @see lighter\handlers.Logger::startProfiling()
     */
    public function startProfiling($label) {
        $this->time[$label] = microtime(true);
    }


    /**
     * @see lighter\handlers.Logger::endProfiling()
     */
    public function endProfiling($label) {
        $endTime = microtime(true);
        if (isset($this->time[$label])) {
            $time = $endTime - $this->time[$label];
            $this->addTimedMessage('Profiling', $label.': '.$time.' s');
        } else {
            throw new UnexpectedValueException('This profiling operation doesn\'t exist.');
        }
    }

}


class DebugLogger extends ProfilerLogger {


    public function __construct($section) {
        parent::__construct($section);
    }


    /**
     * @see lighter\handlers.Logger::debug()
     */
    public function debug($message) {
        $this->addTimedMessage('Debug', $message);
    }

}


class DumpLogger extends DebugLogger {


    public function __construct($section) {
        parent::__construct($section);
    }


    /**
     * @see lighter\handlers.Logger::dump()
     */
    public function dump($variable) {
        $this->addTimedMessage('Dump', '');
        $this->addMessage(print_r($variable, true));
    }

}


class TraceLogger extends DumpLogger {


    public function __construct($section) {
        parent::__construct($section);
    }


    public function trace($title = null) {
        $backtrace = debug_backtrace();
        $this->addTimedMessage('Trace', '');
        foreach ($backtrace as $trace) {
            $this->addMessage($trace["class"].$trace["type"].$trace["function"]);
        }
    }
}


/**
 * The exception lauched by the debug class.
 *
 * @name DebugException
 * @package lighter
 * @subpackage handlers
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class NonexistentProfilingOperation extends Exception {}

