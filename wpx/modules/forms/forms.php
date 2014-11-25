<?php 

class WPX_Forms extends WPX {

	public $posttypes = ['Form'], $taxonomies = [ ['name' => 'Form Type', 'assoc' => 'form'] ];

	private $db;

	public function __construct() {
		
		parent::__construct();

		if(!$this->database()->tableExists('wpx_forms')) {
			$this->install();
		} else {
			$this->database()->setTable('wpx_forms');
		}
		
	}

	private function install() {
			$this->database()->setTable('wpx_forms');
			$this->database()->createTable('wpx_forms', function($table){
				$table->increments('id');
				$table->varchar('email')->null();
				$table->varchar('name')->null();
				$table->varchar('phone')->null();
				$table->text('other_params')->null();
				$table->dateTime('date');
				return $table;
			});
	}

	public function ajx_submitForm() {

		parse_str($_POST['formData'], $formData);

		//serialize(['ttc' => $formData['cbTime']])
		$name = isset($formData['fldName']) ? $formData['fldName'] : '';
		$phone = isset($formData['fldPhone']) ? $formData['fldPhone'] : '';
		$email = isset($formData['fldEmail']) ? $formData['fldEmail'] : '';

		$this->database()->create([
				'name' => $name,
				'phone' => $phone,
				'email' => $email,
				'other_params' => 'Email Signup',
				'date' => date("Y-m-d H:i:s")
			]);

		wp_send_json_success();
		/*
		$template = $this->template('emails/callback', $formData, false);
		$result = $this->mail('james@mvdigital.co', 'TEST', $template);
		*/
		if($result['success']) wp_send_json_success();
		wp_send_json_error($result['error']);

	}

	public function createForm() {
		return $this->template('test', array(), true);
	}



}
new WPX_Forms;