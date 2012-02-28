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
		$this->error 		= isset($_FILES[$file]['error']) ? $_FILES[$file]['error'] : null;
		
		$this->extension = File::extension($this->name);
	}
	
	
	/**
	 * moves uploaded file to the given location (path)
	 *
	 * @param string $path path where file will be placed
	 * @return Boolean
	 */
	public function move($path)
	{
		$this->path = SystemConfig::$filesPath . rtrim($path, '/').'/';
		return File::move($this->tmp_name, $this->path . $this->name);
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
	 */
	public function thumbnail($filepath, $coords, $size, $quality)
	{
		$ocx = (int)$coords['x'];   $ocy = (int)$coords['y'];   $ocx2 = (int)$coords['x2'];   $ocy2 = (int)$coords['y2'];   $ocw = $ocx2 - $ocx;   $och = $ocy2 - $ocy;
		
		$command = $ocw > 0
			? "convert {$this->tmp_name} -crop {$ocw}x{$och}+{$ocx}+{$ocy} -thumbnail {$size} -quality {$quality} $filepath"
			: "convert {$this->tmp_name} -thumbnail {$size}^ -gravity center -crop {$size}+0+0 -quality {$quality} $filepath"
		;
		
		exec($command, $output);
		return $output;
	}

	
	/**
	 * @param $size (string)
	 * Wx 	- same width
	 * xH 	- same height
	 * WxH 	- resize inside
	 * WxH^	- resize outside
	 * WxH>	- resize only larger images
	 * WxH<	- resize only smaller images
	 * WxH!	- do not keep aspect ratio
	 */
	public function resize($filepath, $size, $quality)
	{
		$command = "convert {$this->tmp_name} -resize '{$size}' -quality {$quality} $filepath";
		
		exec($command, $output);
		return $output;
	}
}
?>