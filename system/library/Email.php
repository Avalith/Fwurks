<?php
require_once dirname(__FILE__).'/htmlmimemail/htmlmimemail.php';

class Email
{
	protected $instance;
	
	protected $to;
	
	public function __construct($from, array $to, $subject)
	{
		$this->instance = new htmlMimeMail5();
		
		$this->instance->setFrom($from);
		$this->instance->setSubject($subject);
		
		$this->to = $to;
	}
	
	public function text($body){ $this->instance->setText($body); } 
	public function html($body){ $this->instance->setHTML($body); }
	
	public function send()
	{
		return $this->instance->send($this->to);
	}
}

?>
