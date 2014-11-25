<?php 

use Pimple\Container;
use Katzgrau\KLogger\Logger;

class WPX_Enqueue extends WPX_Container {

	public $scripts, $logger;

	private $defaultsJS = [
				'type' => 'js',
				'deps' => ['jquery'],
				'ver' => '1.0',
				'in_footer' => true
			],
			$defaultsCSS = [
				'type' => 'css',
				'deps' => [],
				'ver' => '1.0',
				'media' => 'screen'
			];

	public function __construct($ajax = false) {

		$this->logger = new Logger(get_template_directory() . '/wpx/logs');

		//$this->container = new Container();
		if(!$this->isVarSet('enqueue.scripts')) {
			$this->setVar('enqueue.scripts', []);
		} 
		if(!$this->isVarSet('enqueue.theme')) {
			$this->setVar('enqueue.theme', false);	
			$this->enqueueThemeScripts();
			$this->run();
		} 
		if(!$this->isVarSet('enqueue.ajax')) {
			$this->setVar('enqueue.ajax', false);	
		} 
			
	}

	public function run() {
		add_action('wp_enqueue_scripts', [$this, 'enqueueAction']);
	}

	public function addAjax() {
		add_action('wp_enqueue_scripts', [$this, 'enqueueAjax']);	
	}

	public function enqueueAction() {

		$ajax = $this->getVar('enqueue.ajax');

		$this->logger->info('Enqueue ajax false: ' . $ajax);
		
		if(sizeof($this->getVar('enqueue.scripts')) > 0) {
			
			foreach($this->getVar('enqueue.scripts') as $script) {

				switch($script['type']) {
					case 'js':
						wp_enqueue_script( $script['name'], $script['src'], $script['deps'], $script['ver'], $script['in_footer'] );
						break;
					case 'css':
						wp_enqueue_style( $script['name'], $script['src'], $script['deps'], $script['ver'], $script['media'] );
						break;
					default:
						$this->logger->error('Invalid script type: ', $script);
						break;
				}

			}
			//$this->logger->info('Scripts Global (' . get_called_class() . ': ', $this->container['enqueue.scripts']);
			
			

			$theme = $this->getVar('enqueue.theme');
			if($theme === false) {
				$this->enqueueThemeScripts();
				$this->setVar('enqueue.theme', true);
			}

			
			
			if($ajax === false) {
				$this->setVar('enqueue.ajax', true);
				
				
			}

			$this->setVar('enqueue.scripts', []);	
		}
		
	}

	public function enqueueDirectory($directory, $recursive = false, $type = 'both') {

		if(!is_dir($directory)) return;

		$files = scandir($directory);

		$types = ($type == 'both') ? ['js', 'css'] : [$type];

		foreach($files as $file) {
			
			$ext = strtolower(pathinfo($directory . '/' . $file, PATHINFO_EXTENSION));

			if(in_array($ext, $types)) {
				
				if($ext === 'js') {
					$script = $this->defaultsJS;
				}
				elseif($ext === 'css') {
					$script = $this->defaultsCSS;
				} else {
					continue;
				}

				$script['name'] = strtolower(str_replace(array('.css', '.js', '.'), array('', '', '-'), $file)); 
				$script['src'] = $this->getFileURI($directory . '/' . $file);
				$this->enqueueScript($script);
			}

		}

	}

	public function enqueueScript($script) {
//		$this->logger->info('Enqueue script: ', $this->container['enqueue.scripts']);
		$scripts = $this->getVar('enqueue.scripts');
		array_push($scripts, $script);
		$this->setVar('enqueue.scripts', $scripts);
		
	}

	public function enqueueAjax() {
		wp_enqueue_script( 'wpx-ajax', get_template_directory_uri() . '/wpx/core/js/ajax.js', ['jquery'], '1.0', false );
		wp_localize_script( 'wpx-ajax', 'WPX', ['ajaxurl' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( "ajaxNonce" )] );
	}

	private function enqueueThemeScripts() {
		$this->enqueueDirectory(get_template_directory() . '/css');
		$this->enqueueDirectory(get_template_directory() . '/js');
		$this->setVar('enqueue.theme', true);
	}

	public function getFileURI($filePath) {
		return '/' . str_replace(ABSPATH, '', $filePath);
	}

	private function setDefaults($script) {

		if($script['type'] == 'js')
			return array_merge($this->defaultsJS, $script);
		if($script['type'] == 'css')
			return array_merge($this->defaultsCSS, $script);

	}
	
	/*
	public function setVar($varname, $val) {
		$this->container[$varname] = $val;
	}

	public function getVar($varname) {
		return $this->container[$varname];
	}
	*/
}