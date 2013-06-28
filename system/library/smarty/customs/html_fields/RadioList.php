<?php

require_once __DIR__.'/Radio.php';

class HtmlForm_RadioListField extends HtmlForm_Field
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
			
			foreach($this->data as $key => $item)
			{
//				$item = (object)$item;
				
				if(is_object($item))	{ $_val = $item->{$this->value_field};		$_title = $item->{$this->title_field}; }
				else 					{ $_val = $key;								$_title = $item; }
				
				$item = new HtmlForm_RadioField(array(
					'name' 				=> $this->name.$key,
					'group_name' 		=> $this->name,
					'value' 			=> $_val, 
					'label_name' 		=> $_title,
					'checked'			=> "$_val" === "{$this->value}",
				));
				
				$html .= '<li>' . $item->field() . $item->label() . '</li>'; 
			}
			$html .= '</ul>';
		}
		
		return $html;
	}
}

?>
