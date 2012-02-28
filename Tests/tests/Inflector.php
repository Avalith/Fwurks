<?php

require_once __DIR__.'/../../system/library/Inflector.php';

class Inflector_TestSuite extends TestSuite 
{
	private $words = array
	(
		// plural	 		=> singular
		'quizzes'			=> 'quiz',
		'matrices'			=> 'matrix',
		'vertices'			=> 'vertex',
		'indices'			=> 'index',
		'oxen'				=> 'ox',
		'aliases'			=> 'alias',
		'statuses'			=> 'status',
		'octopi'			=> 'octopus',
		'viri'				=> 'virus',
		'shoes'				=> 'shoe',
		'tomatoes'			=> 'tomato',
		'buses'				=> 'bus',
		'mice'				=> 'mouse',
		'lice'				=> 'louse',
		'sexes'				=> 'sex',
		'benches'			=> 'bench',
		'dresses'			=> 'dress',
		'dishes'			=> 'dish',
		'halves'			=> 'half',
		'objectives'		=> 'objective',
		'hives'				=> 'hive',
		'lives'				=> 'life',
		'parentheses'		=> 'parenthesis',
		'prognoses'			=> 'prognosis',
		'crises'			=> 'crisis',
		
		'equipment'			=> 'equipment',
		'information'		=> 'information',
		'rice'				=> 'rice',
		'money'				=> 'money',
		'species'			=> 'species',
		'series'			=> 'series',
		'sms'				=> 'sms',
		
		'people' 			=> 'person',
		'men' 				=> 'man',
		'children' 			=> 'child',
		'databases' 		=> 'database',
		'moves' 			=> 'move',
		'news'				=> 'news',
		
		'users'				=> 'user',
		'apples'			=> 'apple',
		
		''			=> '',
	);
	
	
	protected function test__Converting_string_to_singular()
	{
		$this->check($this->words, 'singularize');
	}
	
	protected function test__Converting_string_to_plural()
	{
		$this->check(array_flip($this->words), 'pluralize');
	}
	
	protected function test__Converting_string_to_table_name()
	{
		$words = array
		(
			'apple'				=> 'apples',
			'apples'			=> 'apples',
			'apple_box'			=> 'apple_boxes',
			'apples_box'		=> 'apples_boxes',
			
			'Apple'				=> 'apples',
			'Apples'			=> 'apples',
			'AppleBox'			=> 'apple_boxes',
			'ApplesBox'			=> 'apples_boxes',
			
			'Apple_Box'			=> 'apple_boxes',
			'Apples_Box'		=> 'apples_boxes',
		
			'apples box'		=> 'apples_boxes',
			'Apples Box'		=> 'apples_boxes',
			
			''					=> '',
		);
		
		$this->check($words, 'tableize');
	}
	
	protected function test__Converting_string_to_underscore()
	{
		$words = array
		(
			'apple'				=> 'apple',
			'apple_box'			=> 'apple_box',
			
			'Apple'				=> 'apple',
			'AppleBox'			=> 'apple_box',
			
			'Apple_Box'			=> 'apple_box',
			'apple box'			=> 'apple_box',
			
			''					=> '',
		);
		
		$this->check($words, 'underscore');
	}
	
	protected function test__Converting_string_to_class_name()
	{
		$words = array
		(
			'apple'				=> 'Apple',
			'apple_box'			=> 'AppleBox',
			
			'Apple'				=> 'Apple',
			'AppleBox'			=> 'AppleBox',
			
			'Apple_Box'			=> 'AppleBox',
			'apple box'			=> 'AppleBox',
			
			''					=> '',
		);
		
		$this->check($words, 'classify');
	}
	
	protected function test__Converting_string_to_file_name()
	{
		$words = array
		(
			'apple'				=> 'apple',
			'apple_type'		=> 'apple.type',
			
			'Apple'				=> 'apple',
			'Apple_Type'		=> 'apple.type',
			'apple type'		=> 'apple_type',

			'apple-box'			=> 'apple_box',
					
			''					=> '',
		);
		
		$this->check($words, 'to_file');
	}
	
	protected function test__Converting_string_to_slug()
	{
		$words = array
		(
			'apple'				=> 'apple',
			'apple_box'			=> 'apple_box',
			
			'Apple'				=> 'apple',
			'AppleBox'			=> 'applebox',
			'Apple_Box'			=> 'apple_box',
			'apple box'			=> 'apple_box',
			'apple-box'			=> 'apple-box',
			
			'!@#$%^&*()_+|=\\'	=> '',
		
			''	=> '',
		);
		
		$this->check($words, 'to_slug');
	}
	
	protected function test__Checking_if_string_is_singular()
	{
		$this->check(array_combine( array_values($this->words), $this->words ), 'is_singular');
	}
	
	protected function test__Checking_if_string_is_plural()
	{
		$this->check(array_combine( array_keys($this->words), array_keys($this->words) ), 'is_plural');
	}
	
	private function check($words, $method)
	{
		foreach($words as $word => $correct)
		{
			$result = Inflector::$method($word);
			$result != $correct && $this->error("'$word' must be '$correct' and not '$result'");
		}
	}
}

?>