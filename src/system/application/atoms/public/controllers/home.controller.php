<?php

class Home_Controller extends Atom_Controller
{
	public function action__index()
	{
		
		$listing = new library\scaffolding\Listing('Book');
		$listing->columns = array('id', 'title'/* TODO => array('processor' => 'link')*/);
		
		echo($listing->render());
		d($listing);
		
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
}

?>
