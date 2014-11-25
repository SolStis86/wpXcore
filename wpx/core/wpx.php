<?php

use Pimple\Container;
use Katzgrau\KLogger\Logger;

class WPX extends WPX_Container {

	public $posttypes = [], $taxonomies = [];

	public function __construct() {

		parent::__construct();

		$this->logger = new Logger(get_template_directory() . '/wpx/logs');

		//$this->container = new Container();
		
		$this->bindClass('WPX_Enqueue', 'enqueue');
		$this->bindClass('WPX_Template', 'template');
		$this->bindClass('WPX_Mail', 'mail');
		$this->bindClass('WPX_Shortcode', 'shortcode');
		$this->bindClass('WPX_PostType', 'posttype');
		$this->bindClass('WPX_Taxonomy', 'taxonomy');
		$this->bindClass('WPX_DB', 'db');

		if(get_called_class() !== __CLASS__) {
			$this->init();
		}

		if (defined('DOING_AJAX') && DOING_AJAX) { 
			$this->registerAJAXFuncs();
			check_ajax_referer( 'ajaxNonce', 'nonce' );
			if(false)
				wp_send_json_error( array('error' => 'Invalid AJAX NONCE') );
		}
	}

	public function init() {

		$this->container['enqueue']->enqueueDirectory($this->getExtensionDirectory() . '/js');
		$this->container['enqueue']->enqueueDirectory($this->getExtensionDirectory() . '/css');

		$this->container['enqueue']->run();

		if(!empty($this->posttypes)) {
			foreach($this->posttypes as $type) {
				$this->container['posttype']->addPostType($type);
			}
			$this->container['posttype']->run();
		}

		if(!empty($this->taxonomies)) {
			foreach($this->taxonomies as $tax) {
				$this->container['taxonomy']->addTax($tax['name'], $tax['assoc']);
			}
			$this->container['taxonomy']->run();
		}

	}

	public function enqueue($script) {

		if(isset($script['dir'])) {
			$this->container['enqueue']->enqueueDirectory($script['dir']);
		} elseif(isset($script['script'])) {
			$this->container['enqueue']->enqueueScript($script['script']);
		}

	}

	public function template($template, $vars = array(), $echo = true) {

		$this->container['template']->setTemplateDir($this->getExtensionDirectory() . '/templates');
		return $this->container['template']->render($template, $vars, $echo);

	}

	public function mail($to, $subject, $content) {
		return $this->container['mail']->send($to, $subject, $content);
	}

	public function shortcode($code, $function) {
		$this->container['shortcode']->createShortcode($code, get_called_class(), $function);
	}

	public function postType($name) {
		$this->container['posttype']->addPostType($name);
	}

	public function appendFooter($function) {
		add_action('wp_footer', [$this, $function]);
	}
	public function appendHeader($function) {
		add_action('wp_head', [$this, $function]);
	}

	public function database() {
		return $this->container['db'];
	}

		public function registerAJAXFuncs() {
		
		foreach (get_class_methods(get_called_class()) as $mtd) {

			if(strpos($mtd, 'ajx_') === 0) {
				//error_log($mtd);
				//error_log(str_replace('ajx_', '', $mtd));
				add_action( 'wp_ajax_' . str_replace('ajx_', '', $mtd), array($this, $mtd) ); // ajax for logged in users
				add_action( 'wp_ajax_nopriv_' . str_replace('ajx_', '', $mtd), array($this, $mtd) );	
				/*
				add_action( 'wp_ajax_' . str_replace('ajx_', '', $mtd), array(get_called_class(), $mtd) ); // ajax for logged in users
				add_action( 'wp_ajax_nopriv_' . str_replace('ajx_', '', $mtd), array(get_called_class(), $mtd) );	
				*/
			}
			
		}
	}

	public function logInfo($info, $arr = false) {
		if($arr) {
			$this->logger->info($info, $arr);
			return;
		}
		$this->logger->info($info);
	}

	

	// In Superclass
	public function getExtensionDirectory() {
		if(get_called_class() !== __CLASS__) {
			$reflection = new ReflectionClass($this);	
			return dirname($reflection->getFileName());
		}
 
	}

	

}
new WPX;