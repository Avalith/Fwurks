<?php

class Control_Text extends Control
{
	protected $available_attributes	= array('autocomplete' , 'list' , 'max' , 'maxlength' , 'min' , 'name' , 'pattern' , 'placeholder' , 'step' , 'type' , 'value');
	protected $available_flags		= array('autofocus' , 'checked' , 'multiple' , 'readonly' , 'required', 'disabled');
	
	public $prepend	= array();
	public $append	= array();
	
	// 
	public $span = 12;
	
	protected function _addons_text($string)
	{
		return "<span class=\"add-on\">{$string}</span>";
	}
	
	protected function _addons_icon($string)
	{
		return $this->_addons_text("<i class=\"{$string}\"></i>");
	}
	
	protected function _addons_button($string)
	{
		return "<button type=\"button\" class=\"btn\">{$string}</button>";
	}
	
	protected function _addons_buttons($list)
	{
		$string = '';
		foreach($list as $i)
		{
			$string .= $this->_addons_button($i);
		}
		
		return $string;
	}
	
	protected function addons($list)
	{
		if(is_array($list))
		{
			$string = '';
			foreach($list as $type => $i)
			{
				$string .= $this->{'_addons_'. $type}($i);
			}
		}
		else
		{
			$string = $this->_addons_text($list);
		}
		
		return $string;
	}
	
	public function field()
	{
		$flags		= $this->flags();
		$attributes	= $this->attributes();
		$prepend	= $this->addons($this->prepend);
		$append		= $this->addons($this->append);
		
		$prepend	&& $this->controls_class .= 'input-prepend ';
		$append		&& $this->controls_class .= 'input-append ';
		
		return <<<Html
{$prepend}<input class="span{$this->span}" {$attributes}{$flags}/>{$append}
Html;
	}
}

?>