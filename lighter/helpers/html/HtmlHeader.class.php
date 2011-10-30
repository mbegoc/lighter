<?php
namespace lighter\helpers\html;


use \Exception;


/**
 * manage the HTTP Header. It's a singleton thus it can be accessed from anywhere in
 * the application, as much from the main controller than from a sub-view of a
 * secondary module of a web page.
 *
 * It's main purpose is to collect all the data related to HTML header through the
 * application and to provide the HTML as a string when needed. This way, there's no
 * need to pass many variables to the template engine to set the HTML header.
 *
 * @name HtmlHeader
 * @package lighter
 * @subpackage helpers\html
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class HtmlHeader {
    /**
     * static singleton instance
     *
     * @var lighter\helpers\html\HtmlHeader
     */
    private static $instance;
    /**
     * The js inclusions.
     *
     * @var array
     */
    protected $jsFiles = array();
    /**
     * The css inclusions.
     *
     * @var array
     */
    protected $cssFiles = array();
    /**
     * the HTML title tag content
     *
     * @var string
     */
    protected $title = "";
    /**
     * The canonical tag
     *
     * @var string
     */
    protected $canonical = "";
    /**
     * The charset used in the page
     *
     * @var string
     */
    protected $charset = "";
    /**
     * The description of the page
     *
     * @var string
     */
    protected $description = "";
    /**
     * The keywords of the page, separated by commas as defined by the HTML norm.
     *
     * @var string
     */
    protected $keywords = "";


    private function __construct(){

    }


    /**
     * @return lighter\helpers\html\HtmlHeader
     */
    public function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * title setter
     *
     * @param string $title
     */
    public function setTitle($title){
        $this->title = "<title>$title</title>";
    }


    /**
     * canonical tag for the page setter
     *
     * @param string $url
     */
    public function setCanonicalTag($url){
        $this->canonical = "";
    }


    /**
     * charset setter
     *
     * @param string $charset
     */
    public function setCharset($charset){
        $this->charset = "<meta http-equiv='Content-Type' content='text/html;charset=$charset'/>";
    }


    /**
     * description setter
     *
     * @param string $description
     */
    public function setDescription($description){
        $this->description = "<meta name='description' content='$description'/>";
    }


    /**
     * keywords setter
     *
     * @param string $keywords
     */
    public function setKeywords($keywords){
        $this->keywords = "<meta name='keywords' content='$keywords'/>";
    }


    /**
     * add a css file to the header
     *
     * @param string $file
     * @param mixed $key - default to NULL - use a default key
     * @param string $media
     */
    public function addCssFile($file, $key = NULL, $media = 'all'){
        if($key == NULL){
            $this->cssFiles[] = array($file, $media);
        }else{
            if(!isset($this->cssFiles[$key])){
                $this->cssFiles[$key] = array($file, $media);
            }else{
                throw new HtmlHeaderException("Css file key already exists.", 1);
            }
        }
    }


    /**
     * remove all the previously added css files
     */
    public function resetCssFiles(){
        $this->cssFiles = array();
    }


    /**
     * remove one previously added css file.
     *
     * @param mixed $key
     */
    public function removeCssFile($key){
        unset($this->cssFiles[$key]);
    }


    /**
     * add a js file to the header
     *
     * @param string $file
     * @param mixed $key
     * @param string $type
     */
    public function addJsFile($file, $key = NULL, $type = 'text/javascript'){
        if($key == NULL){
            $this->jsFiles[] = array($type, $file);
        }else{
            if(!isset($this->cssFiles[$key])){
                $this->jsFiles[$key] = array($type, $file);
            }else{
                throw new HtmlHeaderException("Js file key already exists.", 2);
            }
        }
    }


    /**
     * remove all the previously added js files
     */
    public function resetJsFiles(){
        $this->jsFiles = array();
    }


    /**
     * remove one previously added js file.
     *
     * @param mixed $key
     */
    public function removeJsFile($key){
        unset($this->jsFiles[$key]);
    }


    /**
     * display this object i.e. convert it to a string and echo it
     */
    public function display(){
        echo (string)$this;
    }


    /**
     * convert this header to a string
     */
    public function __toString(){
        $s = '<head>';
        $s.= $this->charset;
        $s.= $this->title;
        $s.= $this->canonical;
        foreach($this->cssFiles as $cssFile){
            $s.= '<link type="text/css" rel="stylesheet" href="'.$cssFile[0]
            	.' media="'.$cssFile[1].'"/>';
        }
        foreach($this->jsFiles as $jsFile){
            $s.= '<script type="'.$jsFile[0].'" src="'.$jsFile[1].'"></script>';
        }
        $s.= $this->description;
        $s.= $this->keywords;
        $s.= '</head>';
        return $s;
    }

}


/**
 * an exception thrown by HtmlHeader
 *
 * @name HtmlHeaderException
 * @package lighter
 * @subpackage helpers\html
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class HtmlHeaderException extends Exception {}

