<?php

require_once __DIR__.'/Select.php';

class HtmlForm_TreeField extends HtmlForm_SelectField 
{
	protected $branch_prefix = '&mdash; ';
	
	protected function optTitlePrefix($opt)
	{
		$opt = (object)$opt;
		return str_repeat($this->branch_prefix, ($opt->level > 1 ? $opt->level : 1) - 1);
	}
}

?>