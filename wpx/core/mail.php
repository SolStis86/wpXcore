<?php

use Pimple\Container;

class WPX_Mail {

	
	public $mailer, $container;
	private $lock;

	private $config = array(
			'smtp' => true,
			'host' => '',
			'smtpAuth' => true,
			'username' => '',
			'password' => '',
			'security' => 'ssl',
			'port' => 465
		);
	private $defaults = array(
			'from' => '',
			'fromName' => '',
			'replyTo' => '',
			'html' => true,
		);


	public function __construct() {

		$this->container = new Container();

		$this->mailer = new PHPMailer;
		$this->setConfig();
		$this->setDefaults();
	}

	public function send($to, $subject, $content) {

		if(!$this->checkLock()) {
			$this->mailer->addAddress($to);
			$this->mailer->Subject = $subject;
			$this->mailer->Body = $content;
			return $this->run();	
		} else {
			$this->container['mail.lock'] = true;
		}
		
	}

	private function run() {

		if(!$this->mailer->send()) {
			return array('success' => false, 'error' => $mail->ErrorInfo);
		}
		return array('success' => true);
	}

	private function setDefaults() {

		extract($this->defaults);
		$this->mailer->From = $from;
		$this->mailer->FromName = $fromName;
		$this->mailer->addReplyTo($replyTo);
		$this->mailer->isHTML($html);
	
	}

	private function setConfig() {

		extract($this->config);

		if($smtp) $this->mailer->isSMTP();
		$this->mailer->Host = $host;
		$this->mailer->SMTPAuth = $smtpAuth;
		$this->mailer->Username = $username;
		$this->mailer->Password = $password;
		$this->mailer->SMTPSecure = $security;
		$this->mailer->Port = $port;             
	
	}

	private function checkLock() {
		if(!isset($this->container['mail.lock']))
			$this->container['mail.lock'] = false;

		return $this->container['mail.lock'];
	}

}