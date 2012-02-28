<?php

require_once __DIR__.'/Simple.php';
require_once __DIR__.'/Checkbox.php';

class HtmlForm_FileField extends HtmlForm_SimpleField
{
	protected $class_field = 'file';
	protected $class_field_wrapper = 'file_field';
	protected $path;
	protected $uploaded;
	
	public function field()
	{
		$this->value || $this->value = $this->uploaded['name'];
		
		$this->filename || $this->filename = $this->value;
		if($this->value && $this->value != '.' && $this->extension())
		{
			$file_type = $this->file_type();
		}
		
		$this->mandatory || $delete = new HtmlForm_CheckboxField(array('name' => "upload_delete[{$this->name}]", 'label_name' => '_delete', 'style_wrapper' => ($this->value ? '' : 'display: none;')));
		$this->mandatory || $delete = $delete->html();
		
		return 	"<div class=\"{$this->class_field_wrapper}\">"
				.	"<input type=\"file\" name=\"{$this->name}\" id=\"{$this->id}\" value=\"{$this->value}\" class=\"{$this->class_field} {$this->class}\" rel=\"{$this->rel}\" lang=\"{$this->lang}\" />"
				.	HtmlForm_Field::helper()
				.	$file_type
				.	$delete
				."</div>";
	}
	
	protected function file_type()
	{
		return "<div class=\"file-type ".$this->extension()."\"><a href=\"".rtrim(url_for($this->path), '/')."/{$this->value}\" target=\"_blank\">{$this->filename}</a></div>";
	}
	
	protected function extension(){ return substr($this->value, strrpos($this->value, '.')+1); }
	
	public static function permissions($filepath)
	{
		if(!file_exists($filepath)){ return false; }
		
		$perms = fileperms($filepath);
		
		if		(($perms & 0xC000) == 0xC000){ $type = 'socket'; }
		else if	(($perms & 0xA000) == 0xA000){ $type = 'link'; }
		else if	(($perms & 0x8000) == 0x8000){ $type = 'file'; }
		else if	(($perms & 0x6000) == 0x6000){ $type = 'block'; }
		else if	(($perms & 0x4000) == 0x4000){ $type = 'directory'; }
		else if	(($perms & 0x2000) == 0x2000){ $type = 'character'; }
		else if	(($perms & 0x1000) == 0x1000){ $type = 'pipe'; } 
		
		$oct = decoct($perms);
		return (object)array
		(
			'int'	=> $perms,
			'full'	=> $oct,
			'short'	=> substr($oct, -4),
		
			'type'	=> $type,
			'owner'	=> (object)array
			(
				'read'		=> $perms & 0x0100 ? true : false,
				'write'		=> $perms & 0x0080 ? true : false,
				'run'		=> $perms & 0x0040 ? ($perms & 0x0400 ? 's' : 'x') : ($perms & 0x0800 ? 'S' : false),
			),
			'group'	=> (object)array
			(
				'read'		=> $perms & 0x0020 ? true : false,
				'write'		=> $perms & 0x0010 ? true : false,
				'run'		=> $perms & 0x0008 ? ($perms & 0x0400 ? 's' : 'x' ) : ($perms & 0x0400 ? 'S' : false),
			),
			'other'	=> (object)array
			(
				'read'		=> $perms & 0x0004 ? true : false,
				'write'		=> $perms & 0x0002 ? true : false,
				'run'		=> $perms & 0x0001 ? ($perms & 0x0200 ? 't' : 'x' ) : ($perms & 0x0200 ? 'T' : false),
			),
		);
	}
}

?>