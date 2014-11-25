<?php

class WPX_Theme extends WPX {

	public $object;

	public function __construct() {
		parent::__construct();
		$this->object = get_queried_object();

	}

	public function header() {
		if($this->templateExists('header'))
			return $this->template('header');
	}

	public function sidebar($index) {
		dynamic_sidebar($index);
	}

	public function content() {
		$template = $this->getContentTemplate();
		//print_r($template['data']);
		return $this->template($template['template'], ['data' => $template['data']]);
	}

	public function getFooter() {
		if($this->templateExists('footer'))
			return $this->template('footer');
	}

	public function getTemplate($template) {
		if($this->templateExists($template))
			return $this->template($template);
		echo 'Template (' . $template . ') does not exist!';
	}

	public function loop($args = []) {
		
		if(empty($args)) {

			if(have_posts()) {
				ob_start();
				while(have_posts()) {
					the_post();
					$this->template('loop', $post);
				}
				return ob_get_clean();
			}

		}
	
	}

	public function image($attachment_id) {
		return wp_get_attachment_image( $attachment_id );
	}

	private function getContentType() {
		
		if(isset($this->object->post_type))
			return $this->object;

		if(isset($this->object->term_id))
			return 'archive';

		if(!$this->object)
			return 'home';

	}

	/* TEMPLATE HEIRARCHY LOGIC */
	private function getContentTemplate() {

		global $post;

		$default = 'index';
		$type = $this->getContentType();

		$data = $post;
		//print_r($data);
		switch($type) {

			case 'home':
				$template = ($this->templateExists('home')) ? 'home' : $default;

				break;

			case 'archive':
				$template = ($this->templateExists('term_' . $this->object->term_id) != '') ? 'term_' . $this->object->term_id : 
					$template = ($this->templateExists('term_' . $this->object->slug) != '') ? 'term_' . $this->object->slug : 
						$template = ($this->templateExists('tax_' . $this->object->taxonomy) != '') ? 'tax_' . $this->object->taxonomy :
							 $template = ($this->templateExists('taxonomy') != '') ? 'taxonomy' : $default;
				$data = $this->getAssocObjects();
				break;

			default:
				$template = ($this->templateExists($this->object->ID . '_' . $this->object->post_type)) ? $this->object->ID . '_' . $this->object->post_type : 
					$template = ($this->templateExists($this->object->post_type)) ? $this->object->post_type : $default;
				break;
		}
		//var_dump($this->templateExists('tax_' . $this->object->taxonomy));
		return ['template' => $template, 'data' => $data];

	}
	private function getAssocObjects() {
		$args = array(
		   'tax_query' => array(
		      array(
		         'taxonomy' => $this->object->taxonomy,
		         'field' => 'slug',
		         'terms' => $this->object->slug
		      )
		   )
		);
		return get_posts($args);
	}
	private function templateExists($template) {

		return is_readable((dirname(__FILE__) . '/templates/' . $template . '.html'));
	}

}