<?php

abstract class HtmlForm_Field
{
	protected $type;
	protected $id;
	protected $name;
	protected $value;
	protected $label;
	protected $label_name;
	protected $no_label;
	protected $no_semicolon;
	protected $translate_label = true;
	protected $class;
	protected $label_class;
	protected $helper;
	
	protected $rel;
	protected $lang;
	
	protected $mandatory;
	protected $disabled;
	
	protected $class_wrapper 		= 'input';
	protected $class_helper 		= 'help';
	protected $class_field_wrapper 	= 'field';
	
	protected $class_field 			= 'field';
	
	public function __construct(array $params)
	{
		foreach ($params as $k => $v){ $this->$k = $v; }
		
		$this->id = $this->name ? 'id_'.$this->name : '';
		
		$this->disabled = $this->disabled ? 'disabled="disabled"' : '';
		
		$this->reinit();
	}
	
	public function label()
	{
		if($this->label || !$this->no_label)
		{
			if(!$this->label_name){ $this->label_name = $this->name; }
			if(!($label = Registry()->globals['DATABASE_FIELDS'][$this->label_name]))
			{
				$label = $this->translate_label && $this->label ? $this->label : $this->label_name; 
			}
			return "<label class=\"{$this->label_class}\" for=\"{$this->id}\">$label".($this->no_semicolon ? '' : ':').($this->mandatory ? ' <span>*</span>' : '')."</label>"; 
		}
		return null;
	}
	
	public function helper()
	{
		if($this->helper){ return "<strong class=\"$this->class_helper\">{$this->helper}</strong>"; }
		return null;
	}
	
	protected function innerHtml(){ return $this->label() .' '. $this->field() .' '. $this->helper(); }
	
	public function html(){ return "<div class=\"{$this->class_wrapper}\">".$this->innerHtml().'</div>'; }
	
	
	protected function reinit(){}
	
	
	abstract public function field();
}

?>