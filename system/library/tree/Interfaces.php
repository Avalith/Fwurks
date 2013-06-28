<?php

interface TreeInterface
{
	public function node($id);
	
	public function load($conditions = null);
	
	public function rebuild($conditions = null);
	
	public function reorder($order);
}


interface TreeNodeInterface
{
	public function __construct($params);
	
	public function branches($levels = 0, $self = false, $conditions = null);
	
	public function children($conditions = null);
	
	public function parent();
	
	public function parents($self = false, $conditions = null);
	
	public function isLeaf();
	
	public function isBranchOf($ancestor_id);
	
	public function isChildOf($parent_id);
	
	public function countBranches($levels = 0, $self = false, $conditions = null);
	
	public function countChildren($conditions = null);
}

?>