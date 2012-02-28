<?php

require_once __DIR__.'/Checkbox.php';

class HtmlForm_CheckboxListField extends HtmlForm_Field
{
	protected $data = array();
	protected $class_field_wrapper = 'list';
	
	protected $value_field = 'id';
	protected $title_field = 'title';
	
	protected $filter = false;
	
	public function __construct(array $params)
	{
		parent::__construct($params);
		
		$this->filter && $this->label_class .= ' checkbox-list-filtered-label';
		$this->many_to_many && $this->class_wrapper .= ' many-to-many';
	}
	
	public function label()
	{
		$replacer = $this->filter ? array('for="'.$this->id.'"' => 'for="'.$this->id.'-filter"') : array('<label' => '<strong', '</label>' => '</strong>');
		return strtr(parent::label(), $replacer);
	}
	
	public function field()
	{
		if($this->data)
		{
			$this->class_field_wrapper 	&& $class_field_wrapper 	= "class=\"{$this->class_field_wrapper}\"";
			$this->class 				&& $class 					= "class=\"{$this->class}\"";
			$this->id 					&& $id 						= "id=\"{$this->id}\"";
			$this->rel 					&& $rel						= "rel=\"{$this->rel}\"";
			$this->lang 				&& $lang					= "lang=\"{$this->lang}\"";
			
			if($this->filter)
			{
				$html .= '<div class="checkbox-list-filter" >';
				$html .= '<input type="text" class="filter" id="'.$this->id.'-filter" />';
				$filter_checkbox = new HtmlForm_CheckboxField(array(
					'name' 				=> $this->name.'-show_selected', 
					'class'				=> 'checkboxlist_show_selected',
					'label_name' 		=> 'checkboxlist_show_selected',
				));
				
				$html .= $filter_checkbox->field() . $filter_checkbox->label();  
				$html .= '</div>';
			}
			$html .= "<div $class_field_wrapper><ul $id $class $lang $rel>";
			
			foreach($this->data as $key => $item)
			{
				if(is_object($item))	{ $_val = $item->{$this->value_field}; 		$_title = $item->{$this->title_field}; }
				else 					{ $_val = $key;								$_title = $item; }
				
				$item = new HtmlForm_CheckboxField(array(
					'name' 				=> $this->name.'['.(++$t_increment).']', 
					'field_value' 		=> $_val, 
					'label_name' 		=> $_title,
					'value'				=> $this->value,
					'no_colon'			=> 1,
				));
				$html .= '<li>' . $item->field() . $item->label() . '</li>'; 
			}
			$html .= '</ul></div>';
		}
		
		return $html;
	}
}

?>
