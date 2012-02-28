<?php

abstract class TestSuite
{
	private $errors = array();
	
	public function __construct()
	{
		$reflection = new ReflectionClass(get_class($this));
		foreach($reflection->getMethods(ReflectionMethod::IS_PROTECTED) as $method)
		{
			$method = $method->getName();
			
			if(preg_match('/^test__(.+)/', $method, $matches))
			{
				$total_tests++;
				
				$test_suite = substr(get_class($this), 0, -10);
				$test_case = strtr($matches[1], array('_' => ' '));
				
				$this->errors = array();
				echo "<br /><br /><u>{$test_suite}</u>: <strong>{$test_case} </strong><strong style='color: #DDD;'>".substr(str_pad($test_case, 70, "."), strlen($test_case)).' </strong>';
				$this->$method();
				
				ob_flush();flush();
				
				echo !$this->errors 
					? '<strong style="color: green;">PASSED</strong>'
					: '<strong style="color: red;">PROBLEM</strong><ol style="color: #D00;"><li>'.implode('</li><li>', $this->errors).'</li></ol>';

				$this->errors || $passed_tests++;
				ob_flush();flush();
			}
        }
        
        echo '<h3 style="color: ', 
        (
	        $passed_tests == $total_tests
	        	? "darkgreen;\"><u>$test_suite</u>: all test casses have passed"
	        	: "red;\"><u>$test_suite</u>: $passed_tests out of $total_tests test cases have passed (".round($passed_tests/$total_tests*100)."%)"
        ),'.</h3>';
	}
	
	protected function error($message)
	{
		$this->errors[] = $message;
	}
	
}

?>
