<?php 

use Pimple\Container;
use Katzgrau\KLogger\Logger;

class WPX_Container {

	public $container;

	public function __construct() {
		$this->container = new Container;
	}

	final private function __clone() { }

	public function bindClass($class, $name, $factory = false) {

		if(!isset($this->container[$name])) {
			
			if($factory) {
				
				$this->container[$name] = $this->container->factory(function($c) use ($class) {
		    		return new $class;
				});		
			
			} else {
			
				$this->container[$name] = function($c) use ($class) {
		    		return new $class;
				};		
			
			}
			

		}
	}

	public static function getStaticClass($class) {
		return new $class;
	}

	public function getService($service) {
		return $this->container[$service];
	}

	public function getVar($varname) {
		return $this->container[$varname];
	}

	public function setVar($varname, $val) {
		
		$this->container[$varname] = $val;
		
	}
	public function isVarSet($varname) {
		return isset($this->container[$varname]);
	}

}