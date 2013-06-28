<?php

class SimpleCrypt
{
	private function convert($str, $key = '')
	{
		if($key == ''){ return $str; }
		
		$key = str_replace(' ', '', $key);
		$key_length = strlen($key);
		
		$k = array();
		for($i = 0; $i < $key_length; $i++){ $k[$i] = ord($key{$i}) & 0x11F; }
		
		$j = 0;
		for($i = 0, $len = strlen($str); $i < $len; $i++)
		{
			$e = ord($str{$i});
			$str{$i} = $e & 0xE0 ? chr($e ^ $k[$j]) : chr($e);
			$j++;
			$j = $j == $key_length ? 0 : $j;
		}
		return $str;
	}
	
	public static function crypt($str, $key)
	{
		$key = md5($key);
		
		$str = self::convert($str, $key);
		
		for($i = 0, $len = strlen($str); $i < $len; $i++)
		{
			$char = self::convert($str{$i}, md5($key));
			$_str .= base64_encode($char);
		}
		
		$_str = str_replace('==', '', $_str);
		$_str = base64_encode($_str);
		$_str = self::convert($_str, md5(md5($key)));
		
		return $_str;
	}
	
	public static function decrypt($str, $key)
	{
		$key = md5($key);
		
		$str = self::convert($str, md5(md5($key)));
		$str = base64_decode($str);
		$str = preg_replace('/([\w\d]{2})/', '$1==', $str);
		
		$_str = explode('==', $str);
		
		$str = '';
		foreach($_str as $s)
		{
			if(!$s){ continue; }
			$char = base64_decode($s.'==');
			$str .= self::convert($char, md5($key));
		}
		
		$str = self::convert($str, $key);
		
		return $str;
	}
}
?>