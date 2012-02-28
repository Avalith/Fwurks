<?php

class Page extends ActiveRecord 
{
	protected static $table_engine = SqlTable::ENGINE_INNODB;
	
	protected static $has_i18n = true;
	
	
	protected static function __columnDefinitions()
	{
		return array
		(
			ORMColumn()					->ID(),
			ORMColumn('parent_id')		->number()->unsigned()->default_value(0),
			ORMColumn('slug')			->string()->unique(),
			ORMColumn('nleft')			->number()->unsigned()->default_value(0)->requiered(),
			ORMColumn('nright')			->number()->unsigned()->default_value(0)->requiered(),
			ORMColumn('nlevel')			->number()->unsigned()->default_value(0)->requiered(),
			ORMColumn('navigation')		->boolean(),
			ORMColumn()					->Active(),
			ORMColumn('_editable')		->boolean(1),
			ORMColumn('_deletable')		->boolean(1),
		);
	}
	
	protected static function __i18n_columnDefinitions()
	{
		return array
		(
			ORMColumn('title')			->string(),
			ORMColumn('keywords')		->string(),
			ORMColumn('description')	->string(),
			ORMColumn('content')		->string(),
		);
	}
	
	
	
	protected function before_update_storage(&$attributes)
	{
		$attributes['title'] && $attributes['slug'] = Inflector::to_slug($attributes['slug'] ?: $attributes['title']);
//		$attributes['i18n_locales_storage']['title']['en'] && $attributes['slug'] = Inflector::to_slug($attributes['slug'] ?: $attributes['i18n_locales_storage']['title']['en']);
	}
	
	
	// Tree Stuff
	protected $parent_nleft;
	protected $parent_nright;
	
	protected function before_validation()
	{
		isset($this->storage->parent_id) 		|| $this->storage->parent_id 		= null;
		isset($this->storage->nleft) 			|| $this->storage->nleft			= null;
		isset($this->storage->nright) 			|| $this->storage->nright			= null;
		isset($this->storage->nlevel) 			|| $this->storage->nlevel			= null;
		isset($this->storage->navigation) 		|| $this->storage->navigation		= null;
		
		isset($this->old_storage->parent_id) 	|| $this->old_storage->parent_id 	= null;
		isset($this->old_storage->nleft) 		|| $this->old_storage->nleft		= null;
		isset($this->old_storage->nright) 		|| $this->old_storage->nright		= null;
		isset($this->old_storage->nlevel) 		|| $this->old_storage->nlevel		= null;
		isset($this->old_storage->navigation) 	|| $this->old_storage->navigation	= null;
		
		$this->storage->parent_id = (int)$this->storage->parent_id;
		
		if($this->old_storage->navigation && !isset($this->storage->navigation))
		{
			$this->storage->parent_id 	= 0; 
			$this->storage->nleft 		= 0;
			$this->storage->nlevel 		= 0; 
			$this->storage->nright 		= 0;
		}
		else
		{
			if($this->storage->parent_id != $this->old_storage->parent_id)
			{
				if(($pid = $this->storage->parent_id) > 0 && isset($this->storage->id) && $pid != $this->storage->id)
				{
					$parent = static::get($pid);
					
					$summary = $this->old_storage->nright - $this->old_storage->nleft;
					$summary || $summary = 1;
					
					$this->storage->nleft 	= (int)$parent->nright;
					$this->storage->nright 	= (int)$this->storage->nleft + $summary;
					$this->storage->nlevel 	= (int)$parent->nlevel + 1;
					
					$this->parent_nleft = $parent->nleft;
					$this->parent_nright = $parent->nright;
				}
				else if($this->storage->nlevel)
				{
					$this->storage->parent_id = 0;
					$this->storage->nleft = static::max_nright('navigation=1')->result()->seek(0)->max + 1;
					$this->storage->nright = $this->storage->nleft + 1;
					$this->storage->nlevel = 1;
				}
			}
		}
	}
	
	protected function before_validation_on_create()
	{
		if(!$this->storage->parent_id && $this->storage->navigation)
		{
			$this->storage->parent_id = 0;
			$this->storage->nleft = static::max_nright('navigation=1')->result()->seek(0)->max + 1;
			$this->storage->nlevel = 1;
			$this->storage->nright = $this->storage->nleft + 1;
		}
		elseif(!$this->storage->parent_id && !$this->storage->navigation)
		{
			$this->storage->parent_id 	= 0; 
			$this->storage->nleft 		= 0;
			$this->storage->nright 		= 0;
			$this->storage->nlevel 		= 0; 
		}
	}
	
	protected function after_validation()
	{
		if(!$this->storage->title){ unset($this->errors['slug']); }
//		if(!$this->storage->i18n_locales_storage->title['en']){ unset($this->errors['slug']); }
	}
	
	
	protected function after_save()
	{
		$this->old_storage->nright 	|| $this->old_storage->nright = 0;
		$this->old_storage->nleft 	|| $this->old_storage->nleft = 0;
		
		$summary = $this->old_storage->nright - $this->old_storage->nleft + 1;
		$summary < 2 && $summary = 2;
		
		$table_name = self::table_name();
		
		if($this->parent_nleft && $this->parent_nright)
		{
			// move tree node
			if($this->storage->parent_id > 0 && $this->old_storage->parent_id != $this->storage->parent_id)
			{
				$difference 		= $this->storage->nleft 	- $this->old_storage->nleft;
				$level_difference 	= $this->storage->nlevel 	- $this->old_storage->nlevel;
				
				// make a gap for the moved node at the end of the parent node
				$not_self = "AND id != {$this->storage->id}";
				static::update(array('nleft?sql' 	=> "nleft + {$summary}"))	->where("navigation = 1 AND nleft >= {$this->storage->nleft} $not_self")->run();
				static::update(array('nright?sql' 	=> "nright + {$summary}"))	->where("navigation = 1 AND nleft >= {$this->storage->nleft} $not_self")->run();
				
				// move ancestors of the moved node
				$add = $difference < 0 ? $summary : 0;
				
				$summary > 2 && static::update(array('nleft?sql' => 'nleft + '.($difference-$add), 'nright?sql' => 'nright + '.($difference-$add), 'nlevel?sql' => "nlevel + {$level_difference}"))->where("navigation = 1 AND nleft > ".($this->old_storage->nleft+$add).' AND nright < '.($this->old_storage->nright+$add))->run();
				
				// remove the gap left buy the moved node ONLY IF the element is not new
				if($this->old_storage->nright)
				{
					static::update(array('nleft?sql' 	=> "nleft - {$summary}"))	->where('navigation = 1 AND nleft  >= '	.($this->old_storage->nright+$add))->run();
					static::update(array('nright?sql' 	=> "nright - {$summary}"))	->where('navigation = 1 AND nright >= '	.($this->old_storage->nright+$add))->run();
				}
			}
		}
		else if($this->old_storage->navigation && !$this->storage->navigation)
		{
			// remove all ancestors from navigation
			static::update(array('nleft' => 0, 'nright' => 0, 'nlevel' => 0, 'parent_id' => 0, 'navigation' => 0))->where("nleft > {$this->old_storage->nleft} AND nright < {$this->old_storage->nright} AND navigation = 1")->run();
			
			// remove the gap left buy the moved node
			static::update(array('nleft?sql' 	=> "nleft - {$summary}"))	->where("navigation = 1 AND nleft  >= {$this->old_storage->nright}")->run();
			static::update(array('nright?sql' 	=> "nright - {$summary}"))	->where("navigation = 1 AND nright >= {$this->old_storage->nright}")->run();
		}
	}
	
	protected function after_delete()
	{
		if($this->storage->id)
		{
			$this->_delete("nleft > {$this->storage->nleft} AND nright < {$this->storage->nright} AND navigation = 1" );
			
			$table_name = self::table_name();
			$summary = $this->storage->nright - $this->storage->nleft + 1;
			
			static::update(array('nleft?sql' 	=> "nleft - {$summary}"))	->where("navigation = 1 AND nleft  >= {$this->storage->nright}")->run();
			static::update(array('nright?sql' 	=> "nright - {$summary}"))	->where("navigation = 1 AND nright >= {$this->storage->nright}")->run();
		}
	}

	public static function getParentsSelector($data = null)
	{
		return TreeFactory::create('Nested', static::model_name())
			->load(( (isset($data->id) && $data->id ? "(nleft < {$data->nleft} OR nright > {$data->nright}) AND " : '').' navigation=1' ), false)
			->result();
	}
}

?>
