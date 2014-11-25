<?php

use Pimple\Container;

class WPX_Taxonomy {

	public $container;

	public function __construct() {
		$this->container = new Container();
		if(!isset($this->container['taxonomy.taxs']))
			$this->container['taxonomy.taxs'] = [];
	}

	public function run() {
		add_action('init', [$this, 'registerTaxs']);
	}

	public function registerTaxs() {
		foreach($this->container['taxonomy.taxs'] as $tax) {
			register_taxonomy($tax['name'], [$tax['assoc']], $tax['args']);
		}
		$this->container['taxonomy.taxs'] = [];
	}

	public function addTax($name, $assoc) {
		
		$tax = $this->generateDefaults($name, $assoc);
		$taxs = $this->container['taxonomy.taxs'];
		array_push($taxs, $tax);
		$this->container['taxonomy.taxs'] = $taxs;
	}
	
	private function generateDefaults($name, $assoc) {

		$domain = sanitize_title($name);

		$labels = [
			'name'              => _x( $name . 's', 'taxonomy general name' ),
			'singular_name'     => _x( $name, 'taxonomy singular name' ),
			'search_items'      => __( 'Search ' . $name . 's' ),
			'all_items'         => __( 'All ' . $name . 's' ),
			'parent_item'       => __( 'Parent ' . $name ),
			'parent_item_colon' => __( 'Parent ' . $name . ':' ),
			'edit_item'         => __( 'Edit  ' . $name ),
			'update_item'       => __( 'Update ' . $name ),
			'add_new_item'      => __( 'Add New ' . $name ),
			'new_item_name'     => __( 'New  ' . $name . ' Name' ),
			'menu_name'         => __( $name )
		];

		$args = [
			'hierarchical'          => true,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => [ 'slug' => sanitize_title($name) ]
		];
		return ['name' => $domain, 'assoc' => $assoc, 'args' => $args];
	}

}