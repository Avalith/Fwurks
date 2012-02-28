<?php

class File
{
	/**
	 * Returns file extension
	 *
	 * @param string $string filename
	 * @return string extension
	 */
	public static function extension($string)
	{
		return strtolower(strchr($string, '.') ? substr($string, strrpos($string, '.')+1) : $string);
	}
}
?>