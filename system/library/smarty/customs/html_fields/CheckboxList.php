<?php

require_once dirname(__FILE__).'/Checkbox.php';

class HtmlForm_CheckboxListField extends HtmlForm_Field
{
	protected $data = array();
	protected $class_field_wrapper = 'list';
	
	protected $value_field = 'id';
	protected $title_field = 'title';
	
	public function label()
	{
		return strtr(parent::label(), array('<label' => '<strong', '</label>' => '</strong>'));
	}
	
	public function field()
	{
		if($this->data)
		{
			$html = "<ul id=\"{$this->id}\" class=\"{$this->class_field_wrapper}\" lang=\"{$this->lang}\" rel=\"{$this->rel}\">";
			
			$this->value = is_array($this->value) ? $this->value : explode(',', $this->value); 
			
			foreach($this->data as $key => $item)
			{
				if(is_array($item))
				{
					$_val = $item[$this->value_field];
					$_title = $item[$this->title_field];
				}
				else if(is_object($item))
				{
					$_val = $item->{$this->value_field};
					$_title = $item->{$this->title_field};
				}
				else 
				{
					$_val = $key;
					$_title = $item;
				}
				
				$item = new HtmlForm_CheckboxField(array(
					'name' 				=> $this->name.'['.(++$t_increment).']', 
					'field_value' 		=> $_val, 
					'label_name' 		=> $_title,
					'value'				=> $this->value,
					'no_colon'			=> 1,
				));
				$html .= '<li>' . $item->field() . $item->label() . '</li>'; 
			}
			$html .= '</ul>';
		}
		
		return $html;
	}
}

?>
