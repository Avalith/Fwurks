<?php

class Home_Controller extends Public_Controller
{
	public function index()
	{
		$books = Book::find()
//			->joinRel('Category')
			->joinRel('Reviews')
//			->joinRel('Reviews.AdminUser')
//			->joinRel('Cover')
//			->joinRel('Authors')
//			->joinRel(array('Category', 'Cover'))
//			->joinRel()
//			->debug()
			->result();
		
//		$books->populateRel(array('Category', 'Cover'));
//		$books->populateRel('Reviews');
		$books->populateRel('Authors');
		
//		$books[0]->Category();
//		$books[0]->Reviews();
//		$books[0]->Cover();
//		$books[0]->Authors();
			
		d($books[0], 'Book -> 0', 0, 0);
//		d($books[0]->getCategory()	, 'Book -> Category'	, 0, 0);
//		d($books[0]->getReviews()	, 'Book -> Reviews'		, 0, 0);
//		d($books[0]->getCover()		, 'Book -> Authors'		, 0, 0);
//		d($books[0]->getAuthors()	, 'Book -> Authors'		, 0, 0);
//		d($books, 'Book -> 0', 0, 0);
		
		exit;
		
		$reviews = Review::find()
			->joinRel('Book')
			->joinRel('AdminUser')
//			->joinRel('Book.Category')
			->result();
		
		d($reviews, 'Reviews:', 0, 0);
//		d($reviews[0]->Book()->Category()->title, 'Review -> Book', 0, 0);
//		$reviews[0]->Book()->Category()->title;
		
//		Books
//		Reviews
//		Authors -> AuthorsBooks
//		Users
		
		
		exit;
	}
}

?>
