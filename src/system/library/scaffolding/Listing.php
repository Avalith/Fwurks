<?php

namespace library\scaffolding;

function listing_processor_link($cell, $row, $opts)
{
	return 123;
}

class Listing
{
	protected $model;
	
	public $columns				= array('id', 'title' => array('processor' => 'link'), 'active');
	
	public $has_checkboxes		= false;
	public $has_ordering		= false;
	
	
	public $fetch_conditions	= array();
	public $fetch_default_order	= null;
	public $fetch_limit			= null;
	public $fetch_group			= null;
	
	public $filters				= array();
	public $actions				= array
	(
		'edit'		=> array(),
		'delete'	=> array(),
	);
	
	public $buttons			= array();
	
	/*
		# TODO
		# ? 'colgroups'		=> array(),
		# 'check_action'	=> always true
		
		# 'has_paging'		=> always true,
		# 'has_sorting'		=> always true,
		
		
		
	#	'selector'			=> null, try to get those from columns
		
	##	REFACTOR for the new models
	##	'joins'				=> array(),
	##	'joinRel'			=> array(),
		
		###### If usefull
		'user_can'			=> $this->_user_can_do,
	*/
	
	public function __construct($model)
	{
		$this->model = '\\' . $model;
	}
	
	public function fetch(array $params = array())
	{
		$model = $this->model;
		
		return $model::all();
	}
	
	
	public function render(array $params = array())
	{
		return $this->render_list($params);
	}
	
	public function render_filters()
	{
		$html = '';
		foreach($data as $d)
		{
			$html = '';
		}
		
		return $data;
	}
	
	public function render_list(array $params = array())
	{
		$data = $this->fetch($params);
		
		$html = '';
		foreach($data as $row){ $html .= $this->render_row($row); }
		
		return "<table>{$html}</table>";
	}
	
	protected function render_row($row)
	{
		$html = '';
		$data = null;
		
		foreach($this->columns as $cell => $opts)
		{
			if(is_array($opts))
			{
				$data = $row->$cell;
				
				if($opts['processor'])
				{
					foreach((array)$opts['processor'] as $p)
					{
						$data = listing_processor_link($row->$cell, $row, $opts);
					}
				}
			}
			else
			{
				$data = $row->$opts;
			}
			
			$html .= "<td>{$data}</td>";
		}
		
		foreach($this->actions as $action => $opts)
		{
			// TODO if actionable
			$html .= "<td><a href=\"#\">{$action}</a></td>";
		}
		
		return "<tr>$html</tr>";
	}
}

?>