<?php
namespace lighter\handlers;


use lighter\models\Config;


/**
 * This class can handle php templates. This is inspired by Smarty.
 * You can register objects in it, and then access these objects in the template.
 * Finally, this class can process the template and display it.
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
	 * the registered objects list
	 *
	 * @var array
	 */
	private $objects = array();
	/**
	 * the config object
	 *
	 * @var models\Config
	 */
	private $config;


	/**
	 *
	 */
	public function __construct(){
	    $this->config = Config::getInstance();
	}


	/**
	 * ajouts d'objets aux moteur de template. Lors de l'affichage du site,
	 * il sera possible d'accéder à ces objets dans les templates par
	 * $this->name ou $tplEng->name
	 *
	 * @param string $name
	 * @param VueBase $object un objet de type vue
	 */
	public function addObject($name, $object){
		$this->objects[$name] = $object;
	}


	/**
	 * affichage du template principal
	 */
	public function display($template){
		$tplEng = $this;

		include($this->getTemplate($template));
	}


	/**
	 * retourne le template fournit en paramètre processé
	 *
	 * @param string $template
	 */
	public function get($template){
		$tplEng = $this;

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
	protected function getTemplate($template){
	    $paths = $this->config->getTemplatesPaths();
	    foreach($paths as $path => $ext){
	        if(file_exists($path.$template.$ext)){
	            return $path.$template.$ext;
	        }
	    }
	}


	/**
	 * magic functions
	 * permettent l'accès aux objets référencés
	 *
	 * @param $name
	 */
	public function __isset($name){
		return isset($this->objects[$name]);
	}

	public function __get($name){
		return $this->objects[$name];
	}

	public function __set($name, $value){
		$this->objects[$name] = $value;
	}

	public function __unset($name){
		unset($this->objects[$name]);
	}
}

