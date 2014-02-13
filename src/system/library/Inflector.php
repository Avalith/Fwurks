<?php

namespace library;

/**
 * Inflector
 * 
 * @method singularize returns given string as singular
 * @method pluralize returns given string as plural
 * @method tablelize returns given string as plural and exchanges white spaces with underscore
 * @method underscore returns given string with white spaces exchanged with underscore
 * @method classify converts the string CammelCased
 * @method toFile 
 * @method is_singular checks if the word is singular
 * @method is_plural checks if the word is plural
 */
final class Inflector
{
	public static $uncountables = array('equipment', 'information', 'rice', 'money', 'species', 'series', 'sms' , 'rss');
	
	public static $irregular = array
	(
		'person' 	=> 'people',
		'man' 		=> 'men',
		'child' 	=> 'children',
		'database' 	=> 'databases',
		'move' 		=> 'moves',
		'news'		=> 'news',
		'series'	=> 'series',
	);
	
	public static $singular = array
	(
		'(quiz)zes' 				=> '\\1',
		'(matr)ices' 				=> '\\1ix',
		'(vert|ind)ices' 			=> '\\1ex',
		'^(ox)en'	 				=> '\\1',
		'(alias|status)es' 			=> '\\1',
		'([octop|vir])i' 			=> '\\1us',
		'(shoe)s' 					=> '\\1',
		'(o)es' 					=> '\\1',
		'(bus)es' 					=> '\\1',
		'([m|l])ice' 				=> '\\1ouse',
		'(x|ch|ss|sh)es' 			=> '\\1',
		'(m)ovies' 					=> '\\1ovie',
		'([^aeiouy]|qu)ies' 		=> '\\1y',
		'([lr])ves' 				=> '\\1f',
		'(tive)s' 					=> '\\1',
		'(hive)s' 					=> '\\1',
		'([^f])ves' 				=> '\\1fe',
		'((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he|(cri))ses' => '\\1\\2sis',
		'([ti])a' 					=> '\\1um',
		's' 						=> '',
	);
	
	public static $plural = array
	(
		'(quiz)' 					=> '\1zes',
		'(matr)ix|(vert|ind)ex' 	=> '\1\2ices',
		'(alias|status|bus)'		=> '\1es',
		'(octop|vir)us'				=> '\1i',
		'(m|l)ouse' 				=> '\1ice',
		'(x|ch|ss|sh)' 				=> '\1es',
		'([^aeiouy]|qu)y' 			=> '\1ies',
		'(?:([^f])fe|([lr])f)' 		=> '\1\2ves',
		'sis' 						=> 'ses',
		'([ti])um' 					=> '\1a',
		'(buffal|tomat)o' 			=> '\1oes',
		's' 						=> 's',
		'' 							=> 's'
	);
	
	
	
	/**
	 * _singularize() returns given string as singular
	 *
	 * @param string $string
	 * @return string
	 */
	public static function singularize($string)
	{
		static $cache = array();
		
		$string = trim($string);
		if(isset($cache[$string])){ return $cache[$string]; }
		
		$string = strtolower(trim($string));
		
		foreach(self::$uncountables as $_uncountable)
		{
			if(substr($string, strlen($_uncountable)*-1) == $_uncountable)
			{
				return $cache[$string] = $string;
			}
		}
		
		foreach(self::$irregular as $_singular => $_plural)
		{
			if(preg_match('/'.$_plural.'$/i', $string, $arr))
			{
				return $cache[$string] = preg_replace('/'.$_plural.'$/i', substr($arr[0],0,1).substr($_singular,1), $string);
			}
		}
		
		foreach(self::$singular as $rule => $replacement)
		{
			if(preg_match('/'.$rule.'$/i', $string))
			{
				return $cache[$string] = preg_replace('/'.$rule.'$/i', $replacement, $string);
			}
		}
		
		return $cache[$string] = $string;
	}
	
	/**
	 * _prualize() returns given string as plural
	 *
	 * @param string $string
	 * @return string
	 */
	public static function pluralize($string)
	{
		static $cache = array();
		
		$string = trim($string);
		if(isset($cache[$string])){ return $cache[$string]; }
		
		$lowercased_word = strtolower($string);
		
		foreach(self::$uncountables as $u)
		{
			if(substr($string, strlen($u)*-1) == $u)
			{
				return $cache[$string] = $string;
			}
		}
		
		foreach(self::$irregular as $_singular => $_plural)
		{
			if(preg_match('/'.$_singular.'$/i', $string, $arr))
			{
				return $cache[$string] = preg_replace('/'.$_singular.'$/i', substr($arr[0],0,1).substr($_plural,1), $string);
			}
		}
		
		foreach(self::$plural as $rule => $replacement)
		{
			if(preg_match('/'.$rule.'$/i', $string))
			{
				return $cache[$string] = preg_replace('/'.$rule.'$/i', $replacement, $string);
			}
		}
		
		return $cache[$string] = $string;
	}
	
	/**
	 * checks if the word is singular
	 *
	 * @param string $string
	 * @return bool
	 */
	public static function is_singular($string)
	{
		static $cache = array();
		
		$string = trim($string);
		if(isset($cache[$string])){ return $cache[$string]; }
		
		return $cache[$string] = self::singularize(self::pluralize($string)) == $string;
	}
	
	/**
	 * checks if the word is plural
	 *
	 * @param string $string
	 * @return bool
	 */
	public static function is_plural($string)
	{
		static $cache = array();
		
		$string = trim($string);
		if(isset($cache[$string])){ return $cache[$string]; }
		
		return $cache[$string] = self::pluralize(self::singularize($string)) == $string;
	}
	
	/**
	 * returns given string as plural and exchanges white spaces with underscore
	 *
	 * @param string $string
	 * @return string
	 */
	public static function tableize($string)
	{
		static $cache = array();
		
		$string = trim($string);
		if(isset($cache[$string])){ return $cache[$string]; }
		
		return $cache[$string] = self::pluralize(self::underscore($string));
	}
	
	/**
	 * returns given string with white spaces exchanged with underscore
	 *
	 * @param string $string
	 * @return string
	 */
	public static function underscore($string)
	{
		static $cache = array();
		
		$string = trim($string);
		if(isset($cache[$string])){ return $cache[$string]; }
		
		return $cache[$string] = strtolower(preg_replace
		(
			array('~[^\w\d/]+~',	'/([a-z\d])([A-Z])/',	'/([A-Z]+)([A-Z][a-z])/'),
			array('_',				'\1_\2',				'\1_\2'),
			$string
		));
	}
	
	/**
	 * converts string class_name to ClassName
	 *
	 * @param string $string
	 * @return string
	 */
	public static function classify($string)
	{
		static $cache = array();
		
		$string = trim($string);
		if(isset($cache[$string])){ return $cache[$string]; }
		
		return $cache[$string] = str_replace(' ', '',ucwords(str_replace('_', ' ', str_replace('-', ' ', $string))));
	}
	
	/**
	 * converts ClassName_Type to class_name.type
	 *
	 * @param string $string
	 * @return string
	 */
	public static function to_file($string)
	{
		static $cache = array();
		
		$string = trim($string);
		if(isset($cache[$string])){ return $cache[$string]; }
		
		$type = strtolower(substr($string, strrpos($string, '_')+1));
		
		return $cache[$string] = str_replace('_'.$type, '.'.$type, self::underscore($string));
	}
	
	/**
	 * converts ClassName_Type to class_name.type
	 *
	 * @param string $string
	 * @return string
	 */
	/*
	public static function to_slug($string)
	{
		static $cache = array();
		
		$string = trim($string);
		if(isset($cache[$string])){ return $cache[$string]; }
		
		$string = strtr($string, array
		(
			'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ж' => 'J', 'З' => 'Z', 'И' => 'I', 'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C', 'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sht', 'Ъ' => 'U', 'Ь' => 'Y', 'Ю' => 'Yu', 'Я' => 'Ya', 
			'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ж' => 'j', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sht', 'ъ' => 'u', 'ь' => 'y', 'ю' => 'yu', 'я' => 'ya', 
		));
		
		return $cache[$string] = trim( preg_replace('/[^a-zA-Z\d-]+/ui', '-', strtolower($string)), '-' );
	}
	*/
	
	private function __construct(){}
}

?>
