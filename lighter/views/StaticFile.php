<?php
namespace lighter\views;


use lighter\handlers\Debug;

use lighter\helpers\File;

use lighter\handlers\HttpResponse;

use lighter\helpers\html\HtmlHeader;


/**
 * this view will return the content of a file, trying to correctly set
 * the mime type to correctly serve it to the client.
 *
 * @name StaticFile
 * @package lighter
 * @subpackage views
 * @see lighter\controllers\StaticFile
 * @since 0.1
 * @version 0.1.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class StaticFile extends View {
    /**
     * the file name to return
     * @var string
     */
    private $file;


    /**
     * default constructor
     *
     * @param string $template
     */
    public function __construct() {
        parent::__construct(null, null);
        $this->resetMimeTypes();
    }


    /**
     * file setter
     *
     * @param string $file
     */
    public function setFile($file) {
        $this->file = $file;
        $this->defaultType = File::getMimeType($this->file);
        $this->addMimeType($this->defaultType, 'displayFile');
    }


    /**
     * display file content
     */
    public function displayFile() {
        echo file_get_contents($this->file);
    }

}

