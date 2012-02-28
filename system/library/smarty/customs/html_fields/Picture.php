<?php

require_once __DIR__.'/File.php';
require_once __DIR__.'/Button.php';
require_once __DIR__.'/Hidden.php';

class HtmlForm_PictureField extends HtmlForm_FileField
{
	protected $class_wrapper = 'input croppable';
	protected $class_image = 'file-img';
	protected $prefix = '80x70/';
	
	protected $img_size;
	protected $croppable;
	
	protected $cropped_width = 80;
	protected $cropped_height = 70;
	
	protected $cropper_maxwidth = 500;
	protected $cropper_maxheight = 300;
	
	protected $cropper_coords = array('x' => null, 'y' => null, 'x2' => null, 'y2' => null);
	
	protected $keep_ratio = 1;
	
	public function innerHtml()
	{
		substr($this->value, -1) == '.' && $this->value = '';
		$value = $this->value ? rtrim(url_for($this->path), '/').'/'.$this->prefix.$this->value : 'about:blank';
		
		$pic = '<div class="col preview" '.($this->value ? '' : 'style="display: none;"').">
			<a href=\"".rtrim(url_for($this->path), '/')."/{$this->value}\" style=\"width:{$this->cropped_width}px; height:{$this->cropped_height}px;\" rel=\"image-box\">
				<img src=\"$value\" class=\"{$this->class_image}\" />
			</a>
		</div>";
		
		if($this->croppable)
		{
			$this->keep_ratio && $keep_ratio = 'keep-ratio';
			$cropper = "<img src=\"about:blank\" class=\"crop-img $keep_ratio\" style=\"max-width:{$this->cropper_maxwidth}px; max-height:{$this->cropper_maxheight}px;\" />";
			
			$this->uploaded['public_name'] && $cropper .= "<script type=\"text/javascript\">$(function(){ croppable('{$this->name}', '{$this->uploaded['public_name']}'); });</script>";
			
			$hidden_x = new HtmlForm_HiddenField(array('name' => 'upload_crop_coords['.$this->name.'][x]'	, 'class' => 'coord-x'	, 'value' => $this->cropper_coords['x']));
			$hidden_y = new HtmlForm_HiddenField(array('name' => 'upload_crop_coords['.$this->name.'][y]'	, 'class' => 'coord-y'	, 'value' => $this->cropper_coords['y']));
			$hidden_x2 = new HtmlForm_HiddenField(array('name' => 'upload_crop_coords['.$this->name.'][x2]'	, 'class' => 'coord-x2'	, 'value' => $this->cropper_coords['x2']));
			$hidden_y2 = new HtmlForm_HiddenField(array('name' => 'upload_crop_coords['.$this->name.'][y2]'	, 'class' => 'coord-y2'	, 'value' => $this->cropper_coords['y2']));

			$cropper .= $hidden_x->field().$hidden_y->field().$hidden_x2->field().$hidden_y2->field();
			
			$upload = new HtmlForm_ButtonField(array('type' => 'submit', 'name' => 'upload_croppable['.$this->name.']', 'class' => 'upload', 'value' => Registry::$globals['FORM_BUTTONS']['upload'] ));
			$upload = $upload->field();
		}
		
		$input = '<div class="'.($this->value ? 'col' : '').'">'.parent::innerHtml().$upload.'</div>';
		return $cropper . $pic . $input . '<div class="cleaner"></div>';
	}
	
	protected function file_type(){}
}

?>