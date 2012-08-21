<?php
namespace lighter\helpers;


use finfo;


/**
 * provide generic services related to files
 *
 * @name File
 * @package lighter
 * @subpackage helpers
 * @since 0.1.1
 * @version 0.1.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class File {
    /**
     * a list of mime type corresponding to file extensions
     * this list is necessary because the finfo object can't recognize
     * all the mime types, mostly text ones, like css, js, json, xml, etc
     * @var array
     */
    protected static $extToMime = array(
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
    );


    /**
     * return the mime type of a file
     *
     * @param string $file
     * name of the file that we want to know the mime type
     */
    public static function getMimeType($file) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        if (isset(self::$extToMime[$ext])) {
            return self::$extToMime[$ext];
        } else {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            return $finfo->file($file);
        }
    }

}

