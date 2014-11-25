<?php

/* CORE */
require_once 'core/container.php';
require_once 'core/template.php';
require_once 'core/enqueue.php';
require_once 'core/mail.php';
require_once 'core/shortcodes.php';
require_once 'core/posttypes.php';
require_once 'core/taxonomies.php';
require_once 'core/db.php';
require_once 'core/wpx.php';

/* Example MODULES */
require_once 'modules/forms/forms.php';
require_once 'modules/cssutils/cssutils.php';
require_once 'modules/theme/theme.php';


/*
class WPX_Boot {

	public $services = array('WPX_Modal', 'WPX_Forms');

	function __construct() {

		foreach($this->services as $s) {
			$service = new $s;
			$service->boot(); 
		}
		$scripts = new WPX_Scripts;
		$scripts->register();
		$scripts->boot();

	}

}

new WPX_Boot; 
*/