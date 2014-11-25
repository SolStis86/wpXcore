<?php

use Pimple\Container;
use Katzgrau\KLogger\Logger;

class WPX_Template {

	public $container, $templateDir, $cache = false;

	public function __construct() {

		$this->checkCache();
		
		$this->container = new Container();
		
	} 

	public function setTemplateDir($dir) {

		$this->templateDir = $dir;

		$loader = new Twig_Loader_Filesystem($this->templateDir);

		$this->container['template.twig'] = new Twig_Environment($loader, array(
    		'cache' => $this->cache, 'debug' => true
		)); 
		return $this;

	}

	public function render($template, $vars, $echo = true) {

		if($echo) {
			
			echo $this->container['template.twig']->render(str_replace('.', '/', $template) . '.html', $vars);
			return;
		}
		return $this->container['template.twig']->render(str_replace('.', '/', $template) . '.html', $vars);
	}

	private function checkCache() {
		if(!is_dir(dirname(__FILE__)) && $this->cache) {
			mkdir(dirname(__FILE__) . '/cache');
			$this->cache = dirname(__FILE__) . '/cache';
		}
	}

}