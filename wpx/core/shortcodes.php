<?php

use Pimple\Container;

class WPX_Shortcode {

	public $container;

	public function __construct() {
		$this->$container = new Container();
	}

	public function createShortcode($code, $class, $function) {
		add_shortcode( $code, [$class, $function] );
	}

}