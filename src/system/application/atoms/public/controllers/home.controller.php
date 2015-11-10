<?php

use library\database\FwurksORM\QueryBuilders\Mysql\Builder as QB;

class Home_Controller extends Atom_Controller
{
	public function action__index()
	{
		// $listing = new library\scaffolding\Listing('Book');
		// $listing->columns = array('id', 'title'/* TODO => array('processor' => 'link')*/);
		
		// echo($listing->render());
		// d($listing);
		
		// exit();
		
		// $session = new library\session\Session();
		
		// $session::set('a123', 123);
		// d($session::get('a123'));
		
		// $q = new Query();
		
		// $model = new Book();
		// de(Book::all());
		
		// de($q);
		
		//$rc = new ReflectionClass('Query');
		//de($rc->getDocComment());
	}
	
	public function action__orm()
	{
		echo '<h1>FwurkORM</h1>';
		
		$q = new QB();
		
		$q->from('users');
		$q->from(['a' => 'users']);
		
		$q->select();
		$q->select('a', 'b', $q->raw('w'));
		$q->select(['c' => $q->raw('field'), 'x' => 'a']);
		
		$q->where('a', 1);
		$q->where(['c' => 3, 'b__gt' => 2]);
		$q->where($q->raw('q = ? AND w = ? OR e = ?'), 1, 2, 3);
		
		// $q->having('a', 1);
		// $q->having(['c' => 3, 'b__gt' => 2]);
		// $q->having($q->raw('q = ? AND w = ? OR e = ?'), 1, 2, 3);
		
		$q->group('a', 'b');
		$q->order('a', '-b');
		$q->limit(10, 10);
		
		/*
		SELECT a, b, w, field as c, a as x
		FROM users
		WHERE a = 1 AND c = 3 AND b > 2 AND q = 1 AND w = 2 AND e = 3
		
		GROUP BY a, b
		ORDER BY a, b DESC
		LIMIT 10, 10
		*/
		
		de([
			$q->__toString(),
			// (string)$q,
			$q,
		]);
		
		exit;
	}
}

?>