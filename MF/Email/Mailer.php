<?php
namespace MF\Email;

require_once('phpmailer/class.phpmailer.php');

class Mailer extends \PHPMailer {
	public function __construct() {
		$this->CharSet = 'iso-8859-15';
	}
}
