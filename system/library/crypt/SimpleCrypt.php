<?php

class SimpleCrypt
{
	private static function characters()
	{
		$table = strtr(file_get_contents(__DIR__.'/charmap'), array("\n" => ''));
		$table = self::mbStrToArr($table);
		
		return $table;
	}
	
	private static function mbStrToArr($string)
	{
		$enc = 'utf8';
		$arr = array();
		for($i = 0, $len = mb_strlen($string, $enc); $i < $len; $i++)
		{
			$arr[] = mb_substr($string, $i, 1, $enc);
		}
		
		return $arr;
	}
	
	public static function encode($string)
	{
		$enc = 'utf8';
		$__len = 11;
		
		$table = self::characters();
		
		$string = preg_split('//', $string);
		array_shift($string);
		array_pop($string);
		
		$_str = null;
		foreach($string as $s)
		{
			$_str .= str_pad(decbin(ord($s)), 8, '0', STR_PAD_LEFT);
		}
		$_str = explode("\n", trim(chunk_split($_str, $__len)));
		
		$string = null;
		foreach($_str as $s)
		{
			$string .= $table[bindec(str_pad($s, $__len, '0', STR_PAD_RIGHT))];
		}
		
		return $string;
	}
	
	public static function decode($string)
	{
		$enc = 'utf8';
		$__len = 11;
		
		$table = array_flip(self::characters());
		
		$string = self::mbStrToArr($string);
		
		$_str = null;
		foreach($string as &$s)
		{
			$_str .= str_pad(decbin($table[$s]), $__len, '0', STR_PAD_LEFT);
		}
		$_str = explode("\n", trim(chunk_split($_str, 8)));
		
		
		$string = null;
		foreach($_str as $i => $s)
		{
			$char = bindec($s);
			if($char == 0){ continue; }
			$string .= chr($char);
		}
		
		return $string;
	}
	
	
	
	private static function convert($str, $key = '')
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
	
	public static function crypt($str, $key = null)
	{
		$key = md5($key);
		
		$str = self::convert($str, $key);
		
		$_str = '';
		for($i = 0, $len = strlen($str); $i < $len; $i++)
		{
			$char = self::convert($str{$i}, md5($key));
			$_str .= base64_encode($char);
		}
		
		$_str = str_replace('==', '', $_str);
		$_str = base64_encode($_str);
		$_str = self::convert($_str, md5(md5($key)));
		$_str = self::encode($_str);
		
		
		return $_str;
	}
	
	public static function decrypt($str, $key = null)
	{
		$key = md5($key);
		
		$str = self::decode($str);
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