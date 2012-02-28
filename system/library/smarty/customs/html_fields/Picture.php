<?php

require_once dirname(__FILE__).'/File.php';

class HtmlForm_PictureField extends HtmlForm_FileField
{
	protected $class_image = 'file-img';
	protected $prefix = 'thmb_';
	
	protected $img_size;
	
	public function innerHtml()
	{
		$this->img_size && $size = preg_replace('/(\d+)x(\d+)/ui', 'width: $1px; height: $2px;', $this->img_size);
		
		$pic = strpos($this->value, '.') ? "<div class=\"col preview\" ><a href=\"".url_for(rtrim($this->path, '/'))."/{$this->value}\" rel=\"image-box\"><img src=\"".url_for(rtrim($this->path, '/'))."/{$this->prefix}{$this->value}\" class=\"$this->class_image\" style=\"$size\" /></a></div>" : '';
		$input = strpos($this->value, '.') ? '<div class="col">'.parent::innerHtml().'</div>' : parent::innerHtml();
		
		return $pic . $input . '<div class="cleaner">&nbsp;</div>';
	}
	
	protected function file_type(){}
}

?>
