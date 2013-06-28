<?php

class HtmlForm_TextCheckboxField extends HtmlForm_Field
{
	protected $maxlength;
	protected $class_checkbox_field = 'checkbox';
	public function field()
	{
		$this->value == $this->special_value && $checked = 'checked="checked"';
		$this->value = htmlspecialchars($this->value);
		$this->checkbox_name || $this->checkbox_name = $this->name;
		
		if(!($label = Registry::$globals['DATABASE_FIELDS'][$this->special_value_label]))
		{
			$label = $this->special_value_label;
		}
			return "<div class=\"{$this->class_field_wrapper}\">
				<div class=\"textcheckbox_group\">
					<input type=\"checkbox\" name=\"_{$this->checkbox_name}\" id=\"_{$this->id}\" value=\"{$this->checkbox_value}\" rel=\"{$this->special_value}\" class=\"{$this->class_checkbox_field} {$this->checkbox_class}\" $checked /><label for=\"_{$this->id}\" class=\"checkbox\">{$label}</label>
				</div>	
				<input ".($checked ? " style=\"display:none;\"" : "")."type=\"{$this->type}\" name=\"{$this->name}\" id=\"{$this->id}\" value=\"{$this->value}\" class=\"{$this->class_field} {$this->class}\" rel=\"{$this->rel}\" lang=\"{$this->lang}\" maxlength=\"{$this->maxlength}\" {$this->disabled} {$this->tabindex} placeholder=\"{$this->placeholder}\"/>
				</div>";
	}
}

?>