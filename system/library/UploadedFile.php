<?php

/**
 * UploadedFile
 * 
 * @method move moves uploaded file to the given location (path)
 * @method new_name changes the filename (must be without extension!)
 * @method thumbnail creates thumbnail of an image 
 */
class UploadedFile
{
	public $name;
	public $tmp_name;
	public $type;
	public $size;
	public $error;
	
	public $extension;
	
	protected $path;
	
	public function __construct($file)
	{
		$this->name 		= $_FILES[$file]['name'];
		$this->tmp_name 	= $_FILES[$file]['tmp_name'];
		$this->type 		= $_FILES[$file]['type'];
		$this->size 		= $_FILES[$file]['size'];
		$this->error 		= $_FILES[$file]['error'];
		$this->extension 	= File::extension($this->name);
	}
	
	
	/**
	 * moves uploaded file to the given location (path)
	 *
	 * @param string $path path where file will be placed
	 * @return bool
	 */
	public function move($path, $overwrite = true)
	{
		$this->path = SystemConfig::$filesPath . rtrim($path, '/').'/';
		return move_uploaded_file($this->tmp_name, $this->path . $this->name);
	}
	
	
	
	/**
	 * changes the filename (must be without extension!)
	 *
	 * @param string $name
	 */
	public function new_name($name)
	{
		$this->name = $name.'.'.$this->extension; 
	}
	
	
	/**
	 * creates thumbnail of an image 
	 *
	 * @param string $size WIDTHxHEIGHT in pixels and $prefix string
	 * @return FALSE if no path, else returns imagemagick output
	 */
	public function thumbnail($size = '80x70', $prefix = 'thmb', $position)
	{
		if(!$this->path){ return false; }
		
		$position || preg_match('/\+\d+\+\d+/ui', $position) || $position = '+0+0';
		$position || $gravity = '-gravity center';
		
//		For ImageMagic < 6.3.8-3
//		$dimensions = explode('x', $size);
//		exec("convert {$this->path}{$this->name} -resize {$dimensions[0]}x -resize 'x{$dimensions[1]}<' -gravity center -crop {$size}{$position} +repage {$this->path}{$prefix}_{$this->name}", $output);
		
		exec("convert \"{$this->path}{$this->name}\" -thumbnail {$size}^ $gravity -crop {$size}{$position} +repage \"{$this->path}{$prefix}_{$this->name}\"", $output);
		return $output;
	}
}
?>
