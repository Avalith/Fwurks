<?php

class CustomException extends Exception 
{
	public function __construct($message, $code = null)
	{
		parent::__construct(d($message, null, 1), $code);
	}
	
	public function showTrace()
	{
		foreach($this->getTrace() as $t)
		{
			$trace['   <strong style="color: #600; font-size: 16px;">'.$t['class'].$t['type'].$t['function'].'('.implode(', ', $t['args']).')</strong>   '] = array
			(
				'file' => '<strong style="font-size: 16px;">Line '.$t['line'].':</strong> '.$t['file'],
				'arguments' => $t['args'],
			);
		}
		
		d($trace);
	}
}


?>