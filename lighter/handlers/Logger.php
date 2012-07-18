<?php
namespace lighter\handlers;


use lighter\handlers\Config;

use \Exception;


/**
 * This class aims to messages.

 * @name Logger
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
    /**
     * Logging levels constants
     * @var string
     */
    const TRACE   = 'TraceLogger',
          DUMP    = 'DumpLogger',
          PROFILE = 'ProfileLogger',
          DEBUG   = 'DebugLogger',
          INFO    = 'InfoLogger',
          WARNING = 'WarningLogger',
          ERROR   = 'ErrorLogger',
          FATAL   = 'FatalLogger',
          NONE    = 'Logger';


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
     * @var array
     */
    private $time = array();
    /**
     * the file handler in which the messages are written
     *
     * @var resource
     */
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
     * this method is a convenient method to handle the instances pool of loggers.
     *
     * @static
     * @param string $section
     * @param string $level - provided by class constants
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
     * close de file handler
     */
    public function __destruct() {
        fclose($this->file);
    }


    /**
     * add a message with a time stamp
     *
     * @param string $level
     * @param string $content
     */
    protected function addTimedMessage($level, $content) {
        $this->addMessage('<'.date('Y-m-d h:m:s').'> '.$level.' : '.$content);
    }


    /**
     * add a simple message
     *
     * @param string $content
     */
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
 * implements the fatal level
 *
 * @name FatalLogger
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


/**
 * implements the error level
 *
 * @name ErrorLogger
 * @package lighter
 * @subpackage handlers
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
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


/**
 * implements the warning level
 *
 * @name WarningLogger
 * @package lighter
 * @subpackage handlers
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
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


/**
 * implements the info level
 *
 * @name InfoLogger
 * @package lighter
 * @subpackage handlers
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
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


/**
 * implements the profiling level
 *
 * @name ProfileLogger
 * @package lighter
 * @subpackage handlers
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class ProfileLogger extends WarningLogger {


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


/**
 * implements the debug level
 *
 * @name DebugLogger
 * @package lighter
 * @subpackage handlers
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class DebugLogger extends ProfileLogger {


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


/**
 * implements the dump level
 *
 * @name DumpLogger
 * @package lighter
 * @subpackage handlers
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
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


/**
 * implements the trace level
 *
 * @name TraceLogger
 * @package lighter
 * @subpackage handlers
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
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
 * The exception lauched by the profile level.
 * Thrown when a nonexistent profiling operation is terminated.
 *
 * @name NonexistentProfilingOperation
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

