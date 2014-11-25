<?php 

class WPX_CSSUtils extends WPX {
	
	public function __construct() {

		parent::__construct();
		$this->getService('enqueue')->addAjax();
		$this->appendFooter('addAjaxFront');
		if(is_user_logged_in()) {
			$this->appendHeader('adminBarFix');
		}
		$this->appendHeader('topBarFixedFix');
		$this->appendHeader('addFontHeader');
	}
	
	public function adminBarFix() {
		echo '<style>body{margin-top:51px;}.navbar-fixed-top{top:32px;}</style>';
	}
	public function topBarFixedFix() {
		echo '<style>body{margin-top:51px;}</style>';
	}

	public function addAjaxFront() {
		
		echo '<div id="ajax-active"><div class="spinner"><i class="fa fa-spinner fa-spin"></i></div><div class="ajax-message"></div></div>';
	}

	public function addFontHeader() {
		echo "<link href='http://fonts.googleapis.com/css?family=Arimo:400,700,400italic,700italic|Roboto:400,400italic,700,700italic|Lato:100,400,100italic,400italic' rel='stylesheet' type='text/css'>";
	}
	
}
new WPX_CSSUtils;