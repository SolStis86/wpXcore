<?php

use Pimple\Container;

class WPX_PostType {

	public $container;

	public function __construct() {
		$this->container = new Container();
		if(!isset($this->container['posttypes.types']))
			$this->container['posttypes.types'] = [];
		
	}

	public function run() {
		add_action('init', [$this, 'registerTypes']);
	}

	public function registerTypes() {
		foreach($this->container['posttypes.types'] as $type) {
			register_post_type($type['name'], $type['args']);
		}
		$this->container['posttypes.types'] = [];
	}

	public function addPostType($name) {
		
		$type = $this->generateDefaults($name);
		$types = $this->container['posttypes.types'];
		array_push($types, $type);
		$this->container['posttypes.types'] = $types;
	}

	private function generateDefaults($name) {

		$domain = sanitize_title($name);

		$labels = [
				'name' => _x( $name, $domain ),
		        'singular_name' => _x( $name, $domain ),
		        'add_new' => _x( 'Add New', $name ),
		        'add_new_item' => _x( 'Add New ' . $name, $domain ),
		        'edit_item' => _x( 'Edit ' . $name, $domain ),
		        'new_item' => _x( 'New ' . $name, $domain ),
		        'view_item' => _x( 'View ' . $name, $domain ),
		        'search_items' => _x( 'Search ' . $name . 's', $domain ),
		        'not_found' => _x( 'No ' . $name . 's found', $domain ),
		        'not_found_in_trash' => _x( 'No ' . $name . 's found in Trash', $domain ),
		        'parent_item_colon' => _x( 'Parent ' . $name . ':', $domain ),
		        'menu_name' => _x( $name . 's', $domain )
			];
		$args = [
				'labels' => $labels,
		        'hierarchical' => true,
		        'description' => '',
		        'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'page-attributes' ),
		        'taxonomies' => array( ),
		        'public' => true,
		        'show_ui' => true,
		        'show_in_menu' => true,
		        
		        'show_in_nav_menus' => true,
		        'publicly_queryable' => true,
		        'exclude_from_search' => false,
		        'has_archive' => true,
		        'query_var' => true,
		        'can_export' => true,
		        'rewrite' => true,
		        'capability_type' => 'post'
        	];

        return ['name' => $domain, 'args' => $args];
	}

}



/*
$labels = array( 
        'name' => _x( 'Books', 'book' ),
        'singular_name' => _x( 'Book', 'book' ),
        'add_new' => _x( 'Add New', 'book' ),
        'add_new_item' => _x( 'Add New Book', 'book' ),
        'edit_item' => _x( 'Edit Book', 'book' ),
        'new_item' => _x( 'New Book', 'book' ),
        'view_item' => _x( 'View Book', 'book' ),
        'search_items' => _x( 'Search Books', 'book' ),
        'not_found' => _x( 'No books found', 'book' ),
        'not_found_in_trash' => _x( 'No books found in Trash', 'book' ),
        'parent_item_colon' => _x( 'Parent Book:', 'book' ),
        'menu_name' => _x( 'Books', 'book' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => true,
        'description' => 'Some books',
        'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'page-attributes' ),
        'taxonomies' => array( 'category', 'post_tag', 'page-category', 'test' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        
        
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );
    */