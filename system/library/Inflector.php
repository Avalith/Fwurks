<?php

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
	private static $cache = array();

	public function __call($method, $params)
	{
		$string = $params[0];
		
		if(method_exists($this, '_'.$method))
		{
			if(!self::$cache[$method][$string])
			{
				return self::$cache[$method][$string] = call_user_func_array(array($this, '_'.$method), $params);
			}
			else
			{
				return self::$cache[$method][$string];
			}
		}
		else
		{
			error($method, 'Unknown inflector method');
		}
	}
	

	/**
	 * _singularize() returns given string as singular
	 *
	 * @param string $string
	 * @return string
	 */
	private function _singularize($string)
	{
		$_original_word = $string;
		$lowercased_word = strtolower($string);
		
		$uncountable = array('equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep', 'sms' , 'rss');
		foreach ($uncountable as $_uncountable)
		{
			if(substr($lowercased_word,(strlen($_uncountable)*-1)) == $_uncountable){ return $string; } 
		}
		
		$irregular = array
		(
			'person' 	=> 'people',
			'man' 		=> 'men',
			'child' 	=> 'children',
			'sex' 		=> 'sexes',
			'database' 	=> 'databases',
			'move' 		=> 'moves'
		);
		foreach ($irregular as $_singular => $_plural)
		{
			if(preg_match('/('.$_plural.')$/i', $string, $arr))
			{
				return preg_replace('/('.$_plural.')$/i', substr($arr[0],0,1).substr($_singular,1), $string);
			}
		}
		
		$singular = array
		(
			'/(quiz)zes$/i' 				=> '\\1',
			'/(matr)ices$/i' 				=> '\\1ix',
			'/(vert|ind)ices$/i' 			=> '\\1ex',
			'/^(ox)en/i' 					=> '\\1',
			'/(alias|status|wax)es$/i' 		=> '\\1',
			'/([octop|vir])i$/i' 			=> '\\1us',
			'/(cris|ax|test)es$/i' 			=> '\\1is',
			'/(shoe)s$/i' 					=> '\\1',
			'/(o)es$/i' 					=> '\\1',
			'/(bus)es$/i' 					=> '\\1',
			'/([m|l])ice$/i' 				=> '\\1ouse',
			'/(x|ch|ss|sh)es$/i' 			=> '\\1',
			'/(m)ovies$/i' 					=> '\\1ovie',
			'/(s)eries$/i' 					=> '\\1eries',
			'/([^aeiouy]|qu)ies$/i' 		=> '\\1y',
			'/([lr])ves$/i' 				=> '\\1f',
			'/(tive)s$/i' 					=> '\\1',
			'/(hive)s$/i' 					=> '\\1',
			'/([^f])ves$/i' 				=> '\\1fe',
			'/(^analy)ses$/i' 				=> '\\1sis',
			'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\\1\\2sis',
			'/([ti])a$/i' 					=> '\\1um',
			'/(n)ews$/i' 					=> '\\1ews',
			'/s$/i' 						=> '',
		);
		foreach ($singular as $rule => $replacement)
		{
			if (preg_match($rule, $string))
			{
				return preg_replace($rule, $replacement, $string);
			}
		}
		
		return $_original_word;
	}
	

	/**
	 * _prualize() returns given string as plural
	 *
	 * @param string $string
	 * @return string
	 */
	private function _pluralize($string)
	{
		$_original_word = $string;
		$lowercased_word = strtolower($string);
		
		$uncountable = array('equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep' , 'sms' , 'rss');
		foreach ($uncountable as $_uncountable)
		{
			if(substr($lowercased_word,(-1*strlen($_uncountable))) == $_uncountable)
			{
				return $string;
			}
		}
		
		$irregular = array
		(
			'person' 	=> 'people',
			'man' 		=> 'men',
			'child' 	=> 'children',
			'sex' 		=> 'sexes',
			'move' 		=> 'moves'
		);
		foreach ($irregular as $_plural=> $_singular)
		{
			if (preg_match('/('.$_plural.')$/i', $string, $arr))
			{
				return preg_replace('/('.$_plural.')$/i', substr($arr[0],0,1).substr($_singular,1), $string);
			}
		}
		
		$plural = array
		(
			'/(quiz)$/i' 					=> '\1zes',
			'/^(ox)$/i' 					=> '\1en',
			'/([m|l])ouse$/i' 				=> '\1ice',
			'/(matr|vert|ind)ix|ex$/i' 		=> '\1ices',
			'/(x|ch|ss|sh)$/i' 				=> '\1es',
			'/([^aeiouy]|qu)y$/i' 			=> '\1ies',
			'/(hive)$/i' 					=> '\1s',
			'/(?:([^f])fe|([lr])f)$/i' 		=> '\1\2ves',
			'/sis$/i' 						=> 'ses',
			'/([ti])um$/i' 					=> '\1a',
			'/(buffal|tomat)o$/i' 			=> '\1oes',
			'/(bu)s$/i' 					=> '\1ses',
			'/(alias|status)/i'				=> '\1es',
			'/(octop|vir)us$/i'				=> '\1i',
			'/(ax|test)is$/i'				=> '\1es',
			'/s$/i' 						=> 's',
			'/$/' 							=> 's'
		);
		foreach ($plural as $rule => $replacement)
		{
			if (preg_match($rule, $string))
			{
				return preg_replace($rule, $replacement, $string);
			}
		}
		
		return $_original_word;
	}
	
	
	/**
	 * returns given string as plural and exchanges white spaces with underscore
	 *
	 * @param string $string
	 * @return string
	 */
	private function _tableize($string)
	{
		return $this->pluralize($this->underscore($string));
	}
	

	/**
	 * returns given string with white spaces exchanged with underscore
	 *
	 * @param string $string
	 * @return string
	 */	
	private function _underscore($string)
	{
		return strtolower(preg_replace
		(
			array('/[^A-Z^a-z^0-9^\/]+/',	'/([a-z\d])([A-Z])/',	'/([A-Z]+)([A-Z][a-z])/'),
			array('_',						'\1_\2',				'\1_\2'),
			$string
		));
	}

	
	/**
	 * converts the string CammelCased
	 *
	 * @param string $string
	 * @return string
	 */	
	private function _classify($string)
	{
		
		return str_replace(' ', '',ucwords(str_replace('_', ' ', $string)));
	}

	private function _toFile($string)
	{
		$type = strtolower(substr($string, strrpos($string, '_')+1));
		return str_replace('_'.$type, '.'.$type, $this->underscore($string));
	}

	
	/**
	 * checks if the word is singular
	 *
     * @param string $string
     * @return bool
	 */
	private function _is_singular($string)
    {
        return $this->singularize($this->pluralize($string)) == $string;
    }
    
    
    /**
     * checks if the word is plural
     *
     * @param string $string
     * @return bool
     */
    private function _is_plural($string)
    {
        return $this->pluralize($this->singularize($string)) == $string;
    }
	
	private function _to_slug($string)
	{
		$string = strtr($string, array
		(
			'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ж' => 'J', 'З' => 'Z', 'И' => 'I', 'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C', 'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sht', 'Ъ' => 'U', 'Ь' => 'Y', 'Ю' => 'Yu', 'Я' => 'Ya', 
			'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ж' => 'j', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sht', 'ъ' => 'u', 'ь' => 'y', 'ю' => 'yu', 'я' => 'ya', 
		));
		return trim( preg_replace('/[^a-zA-Z\d-]+/ui', '_', strtolower(trim($string))), '_' );
	}
	
	
	/**
	 * Instance
	 */
    private function __construct(){}
	private static $instance;
	public static function getInstance()
	{
		if(!self::$instance){ self::$instance = new Inflector(); }
		return self::$instance;
	}
}
function Inflector(){ return Inflector::getInstance(); }


?>
