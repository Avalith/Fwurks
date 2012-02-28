<?php

class Pages extends ActiveRecord 
{
	protected static $has_i18n = true;
	
	protected function before_update_storage(&$attributes)
	{
		$attributes['title'] && $attributes['slug'] = Inflector::to_slug($attributes['slug'] ?: $attributes['title']);
//		$attributes['i18n_locales_storage']['title']['en'] && $attributes['slug'] = Inflector::to_slug($attributes['slug'] ?: $attributes['i18n_locales_storage']['title']['en']);
	}
	
	
	protected $parent_nleft;
	protected $parent_nright;
	
	protected function before_validation()
	{
		$this->storage->parent_id = (int)$this->storage->parent_id;
		
		if($this->old_storage->navigation && !$this->storage->navigation)
		{
			$this->storage->parent_id 	= 0; 
			$this->storage->nleft 		= 0;
			$this->storage->nlevel 		= 0; 
			$this->storage->nright 		= 0;
		}
		else
		{
			if($this->old_storage->parent_id != $this->storage->parent_id)
			{
				$tree = TreeFactory::create('Nested', array('pages'));
				
				if(($pid = $this->storage->parent_id) > 0 && $pid != $this->storage->id)
				{
					$parent = $tree->node($pid);
					
					$summary = $this->old_storage->nright - $this->old_storage->nleft;
					$summary || $summary = 1;
					
					$this->storage->nleft 	= (int)$parent->nright;
					$this->storage->nright 	= (int)$this->storage->nleft + $summary;
					$this->storage->nlevel 	= (int)$parent->nlevel + 1;
					
					$this->parent_nleft = $parent->nleft;
					$this->parent_nright = $parent->nright;
				}
				else if(!$this->storage->nlevel)
				{
					$this->storage->parent_id = 0;
					$this->storage->nleft = self::find_max_nright() + 1;
					$this->storage->nlevel = 1;
					$this->storage->nright = $this->storage->nleft + 1;
				}
			}
		}
	}
	
	protected function before_validation_on_create()
	{
		if(!$this->storage->parent_id && $this->storage->navigation)
		{
			$this->storage->parent_id = 0;
			$this->storage->nleft = self::find_max_nright('navigation=1') + 1;
			$this->storage->nlevel = 1;
			$this->storage->nright = $this->storage->nleft + 1;
		}
		elseif(!$this->storage->parent_id && !$this->storage->navigation)
		{
			$this->storage->parent_id 	= 0; 
			$this->storage->nleft 		= 0;
			$this->storage->nlevel 		= 0; 
			$this->storage->nright 		= 0;
		}
	}
	
	protected function after_validation()
	{
		if(!$this->storage->title){ unset($this->errors['slug']); }
//		if(!$this->storage->i18n_locales_storage->title['en']){ unset($this->errors['slug']); }
	}
	
	
	protected function after_save()
	{
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
				self::$db->query("UPDATE {$table_name} SET nleft = nleft + {$summary} 	WHERE navigation = 1 AND nleft >= {$this->storage->nleft} $not_self");
				self::$db->query("UPDATE {$table_name} SET nright = nright + {$summary} 	WHERE navigation = 1 AND nright >= {$this->storage->nleft} $not_self");
				
				// move ancestors of the moved node
				$add = $difference < 0 ? $summary : 0;  
				$summary > 2 && self::$db->query("UPDATE {$table_name} SET nleft = (nleft + {$difference})-{$add}, nright = (nright + {$difference})-{$add}, nlevel = nlevel + {$level_difference} WHERE navigation = 1 AND nleft > {$this->old_storage->nleft}+{$add} AND nright < {$this->old_storage->nright}+{$add}");
				
				// remove the gap left buy the moved node ONLY IF the element is not new
				if($this->old_storage->nright)
				{
					self::$db->query("UPDATE {$table_name} SET nleft = nleft - {$summary} WHERE navigation = 1 AND nleft >= {$this->old_storage->nright}+{$add}");
					self::$db->query("UPDATE {$table_name} SET nright = nright - {$summary} WHERE navigation = 1 AND nright >= {$this->old_storage->nright}+{$add}");
				}
			}
		}
		else if($this->old_storage->navigation && !$this->storage->navigation)
		{
			// remove all ancestors from navigation
			self::$db->query("UPDATE {$table_name} SET nleft = 0, nright = 0, nlevel = 0, parent_id = 0, navigation = 0 WHERE nleft > {$this->old_storage->nleft} AND nright < {$this->old_storage->nright} AND navigation = 1" );
			
			// remove the gap left buy the moved node
			self::$db->query("UPDATE {$table_name} SET nleft = nleft - {$summary} WHERE navigation = 1 AND nleft >= {$this->old_storage->nright}");
			self::$db->query("UPDATE {$table_name} SET nright = nright - {$summary} WHERE navigation = 1 AND nright >= {$this->old_storage->nright}");
		}
	}
	
	protected function after_delete()
	{
		if($this->storage->id)
		{
			$this->_delete("nleft > {$this->storage->nleft} AND nright < {$this->storage->nright} AND navigation = 1" );
			
			$table_name = self::table_name();
			$summary = $this->storage->nright - $this->storage->nleft + 1;
			self::$db->query("UPDATE {$table_name} SET nleft = nleft - {$summary} WHERE navigation = 1 AND nleft >= {$this->storage->nright}");
			self::$db->query("UPDATE {$table_name} SET nright = nright - {$summary} WHERE navigation = 1 AND nright >= {$this->storage->nright}");
		}
	}
}

?>
