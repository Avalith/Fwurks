<?php

class Paging
{
	public $page = 1;
	public $rpp = 10;
	
	// always odd
	public $pages_to_show = 10;  
	
	public $limit;
	
	public $show_numbered = true;
	
	public function __construct($page, $rpp = 10)
	{
		$page < 1 && $page = 1;
			
		$page 	&& $this->page 	= $page;
		$rpp 	&& $this->rpp 	= $rpp;
		
		$this->limit = ($this->page-1)*$this->rpp.', '.$this->rpp;
	}
	
	public function build($total)
	{
		if($total <= $this->rpp){ return false; }
		
		$page = $this->page;
		$rpp = $this->rpp;
		$pts = $this->pages_to_show;
		
		$page > 1 && $page_first = 1;
		$page_last = ceil($total/$rpp);

		$page > 1 && $page_prev = $page - 1;
		$page < $page_last && $page_next = $page + 1;
		
		$page_prev_dots = false;
		$page_next_dots = false;
		
		// starts cutted paging
		if($pts){ $pts & 1 || $pts++; } // keep pages to show always odd
		
		$end = $page_last;
		if($pts && $pts < $end)
		{
			
			$half_pts = floor($pts/2);
			
			$end = $page + $half_pts;
			$start = $page - $half_pts;
			
			if($page <= $half_pts+1)
			{
				$start = 1;
				$end += $half_pts - $page + 1;
			}
			else if($this->show_numbered)
			{
				// page next number and dots
				$page_prev_num = $page - $this->pages_to_show;
				$page_prev_num < 1 && $page_prev_num = 1;
			}
			
			if($page_last - $page <= $half_pts)
			{
				$end = $page_last;
				$start -= $half_pts - ($page_last - $page);
			}
			else if($this->show_numbered)
			{
				// page prev number and dots
				$page_next_num = $page + $this->pages_to_show;
				$page_next_num > $page_last && $page_next_num = $page_last;
			}
			
		}
		// ends cutted paging
		
		$start || $start = 1;
		for($p = $start; $p <= $end; $p++){ $pages[] = $p; }
		
		return array
		(
			'current_page'		=> $page,
			'page_first' 		=> $page_first,
			'page_prev_num'		=> $page_prev_num,
			'page_prev'			=> $page_prev,
			'pages'				=> $pages,
			'page_next'			=> $page_next,
			'page_next_num'		=> $page_next_num,
			'page_last'			=> $page != $page_last ? $page_last : 0,
			'total_pages'		=> $page_last,
			'pages_to_show'		=> $pts < $page_last ? $this->pages_to_show : 0,
			
			'show_numbered'		=> $this->show_numbered,
			'records_per_page'	=> $this->rpp,
		);
	}
	
}

?>