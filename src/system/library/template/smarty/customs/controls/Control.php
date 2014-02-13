<?php

// Inputs Types
// [ ] button
// [ ] checkbox
// [ ] color
// [ ] date
// [ ] datetime
// [ ] datetime-local
// [ ] email
// [ ] file
// [ ] hidden
// [ ] image
// [ ] month
// [ ] number
// [x] password
// [ ] radio
// [ ] range
// [ ] reset
// [ ] search
// [ ] submit
// [ ] tel
// [x] text
// [ ] time
// [ ] url
// [ ] week
// [ ] 
// [ ] 
// [ ] 


// NonInput
// [ ] Textarea
// [ ] Select

// Special
// [ ] Tags
// [ ] ListCheck
// [ ] ListRadio
// [ ] 



abstract class Control
{
	protected $available_attributes	= array();
	protected $available_flags		= array();
	
	public $status;
	
	final public function __construct($params)
	{
		foreach($params as $key => $value){ $this->$key = $value; }
		
		// $this->init();
	}
	
	// public function init(){}
	
	abstract public function field();
	
	public function label()
	{
		return <<<Html
<label class="control-label">{$this->label}</label>
Html;
	}
	
	public function helper()
	{
		if($this->helper)
		{
			return <<<Html
<span class="help-block $">{$this->helper}</span>
Html;
		}
	}
	
	public function html()
	{
		$label		= $this->label();
		$field		= $this->field();
		$helper		= $this->helper();
		
		return <<<Html
<div class="control-group {$this->status}">{$label}<div class="controls {$this->controls_class}">{$field}{$helper}</div></div>
Html;
	}
	
	protected function flags()
	{
		$string = '';
		foreach($this->available_flags as $k){ if($this->$k){ $string .= $k . ' '; } }
		
		return $string;
	}
	protected function attributes()
	{
		$string = '';
		foreach($this->available_attributes as $k){ if($this->$k){ $string .= $k . '="' . $this->$k . '" '; } }
		
		return $string;
	}
}

?>