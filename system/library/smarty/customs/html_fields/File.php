<?php

require_once dirname(__FILE__).'/Simple.php';
require_once dirname(__FILE__).'/Checkbox.php';

class HtmlForm_FileField extends HtmlForm_SimpleField
{
	protected $class_field = 'file';
	protected $class_field_wrapper = '';
	protected $path;
	
	public function field()
	{
		if($this->value)
		{
			$delete = new HtmlForm_CheckboxField(array('name' => "__delete[{$this->name}]", 'label_name' => '_delete'));
			$delete = $delete->html();
			$file_type = $this->file_type();
		}
		
		return 	"<div class=\"{$this->class_field_wrapper}\">"
				.	"<input type=\"file\" name=\"{$this->name}\" id=\"{$this->id}\" value=\"{$this->value}\" class=\"{$this->class_field} {$this->class}\" rel=\"{$this->rel}\" lang=\"{$this->lang}\" />"
				.	HtmlForm_Field::helper()
				.	$file_type
				.	$delete
				."</div>";
	}
	
	protected function file_type()
	{
		return "<div class=\"file-type ".$this->extension()."\"><a href=\"".rtrim($this->path, '/')."/{$this->value}\" target=\"_blank\">{$this->value}</a></div>";
	}
	
	protected function extension(){ return substr($this->value, strrpos($this->value, '.')+1); }
	
	public function helper(){}
	
}

?>