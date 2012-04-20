<?php
namespace lighter\handlers;


use lighter\handlers\Config;


/**
 * This class can handle php templates. This is inspired by Smarty.
 * You can register vars in it, and then access these vars in the template.
 * Finally, this class can process the template and display it.
 *
 * This object is accessible in the templates as $this.
 *
 * @name TemplateEngine
 * @package lighter
 * @subpackage handlers
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class TemplateEngine {
    /**
     * the registered vars list
     *
     * @var array
     */
    private $vars = array();
    /**
     * the config object
     *
     * @var models\Config
     */
    private $config;


    /**
     *
     */
    public function __construct() {
        $this->config = Config::getInstance();
    }


    /**
     * ajouts d'objets aux moteur de template. Lors de l'affichage du site,
     * il sera possible d'accéder à ces objets dans les templates par
     * $this->name ou $tplEng->name
     *
     * @param string $name
     * @param mixed $var
     */
    public function addVar($name, $var) {
        $this->vars[$name] = $var;
    }


    /**
     * affichage du template principal
     */
    public function display($template) {
        extract($this->vars);

        include($this->getTemplate($template));
    }


    /**
     * retourne le template fournit en paramètre processé
     *
     * @param string $template
     */
    public function get($template) {
        extract($this->vars);

        ob_start();
        include($this->getTemplate($template));
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }


    /**
     *
     * @param string $template
     */
    protected function getTemplate($template) {
        $paths = $this->config->getSection('tplPaths');
        foreach ($paths as $path) {
            if (file_exists($path.$template.'.php')) {
                return $path.$template.'.php';
            }
        }
    }

}

