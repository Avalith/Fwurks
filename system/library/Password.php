<?php

class Password
{
	public static function hash($pass, $salt)
	{
		return crypt($pass, '$2y$11$' . self::generate_salt($salt));
	}
	
	private static function generate_salt($salt)
	{
		$possible = "YPebB9tdMCZFO3XSLoWkVpgm1rKQi7Hnz2Nsq4U5uh08aGDRATycElxIw6jvJf"; #echo str_shuffle($possible); exit;
		$possible_count = strlen($possible);

		$salted = '';
		for($i = 0, $len = strlen($salt), $base = 0; strlen($salted) < 22; $i++, $base += $len)
		{
			if($i >= $len){ $i = 0; }
			$salted .= $possible{(ord($salt{$i}) + $base) % $possible_count};
		}

		return $salted;
	}
}

?>
