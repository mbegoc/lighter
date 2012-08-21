<?php
namespace lighter\views;


use lighter\handlers\Debug;

use lighter\handlers\HttpResponse;
use lighter\handlers\TemplateEngine;

use lighter\helpers\html\HtmlHeader;

use \Exception;


/**
 * a dedicated view to handle specific aspect of a complete web page
 *
 * @name WebPage
 * @package lighter
 * @subpackage views
 * @see lighter\views\View
 * @since 0.1
 * @version 0.1.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
abstract class WebPage extends View {
    /**
     * the title of the page
     * @var string
     */
    protected $title = '';
    /**
     * the template of the content of the page
     * @var string
     */
    protected $contentTemplate = null;
    /**
     * the html header of the page
     * @var lighter\helpers\html\HtmlHeader
     */
    protected $htmlHeader = null;


    /**
     * default constructor
     *
     * @param string $mainTemplate
     * @param string $contentTemplate
     */
    public function __construct($mainTemplate, $contentTemplate) {
        parent::__construct($mainTemplate);
        $this->contentTemplate = $contentTemplate;
        $this->htmlHeader = new HtmlHeader();
    }


    /**
     * @see lighter\views.View::displayHtml()
     */
    protected function displayHtml() {
        $this->tplEngine->addVar('htmlHeader', $this->htmlHeader);
        $this->tplEngine->addVar('debug', Debug::getInstance()->prepareView($this->htmlHeader));
        parent::displayHtml();
    }


    /**
     * return the HTML actual content of the page
     *
     * @return string
     */
    public function getMainContent() {
        return $this->tplEngine->get($this->contentTemplate);
    }


    /**
     * title setter
     *
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }


    /**
     * title getter
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }


    /**
     * html header getter
     *
     * @return lighter\helpers\html\HtmlHeader
     */
    public function getHtmlHeader() {
        return $this->htmlHeader;
    }

}
