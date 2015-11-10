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
		
		$q->table('users');
		// ------ $q->table(['a' => 'users']);
		
		$q->select();
		$q->select('a', 'b', $q->raw('c'));
		$q->select(['c' => $q->raw('field'), 'x' => 'a']);
		
		$q->where('a', 1);
		$q->where(['c' => 3, 'b__gt' => 2]);
		$q->where($q->raw('q = ? AND w = ? OR e = ?'), 1, 2, 3);
		
		$q->limit(10, 10);
		$q->order('a', '-b');
		$q->group('a', 'b');
		
		
		
		de([
			$q->sql(),
			$q,
		]);
		
		exit;
	}
}

?>