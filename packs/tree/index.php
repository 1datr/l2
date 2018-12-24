<?php
namespace tree{
	
	
	use hnumerator\HNnumerator;
	
	// общий класс узел дерева	самый базовый класс. Для всех деревьев
	class tree_node
	{
		VAR $_ITEMS=[];
		VAR $_STANDSTILL=FALSE; // тупиковая ветвь
		VAR $number=NULL;
		VAR $numerator_obj;
		VAR $_PARENT=NULL;
		VAR $index_in_parent=0;
		
	
		function add_item(&$item)
		{		
			$new_item_idx = count($this->_ITEMS);
			$this->_ITEMS[]=$item;
			$item->_PARENT=$this;
			$item->index_in_parent = $new_item_idx;
		}
		
		function numerate()
		{
			$the_node=$this->prev();
			if($the_node==null)
			{
				$the_node=$this->_PARENT;
				if($the_node==null)
				{
					$obj_num = new HNnumerator();
				}
				else 
				{
					$obj_num = clone $the_node->get_numerator();
					$obj_num->down();
				}
			}
			else	// has previous brother 
			{
				$obj_num = clone $the_node->get_numerator();
				$obj_num->inc();
			}
			$this->numerator_obj=$obj_num;
			$this->number=$obj_num->getText();
			foreach ($this->_ITEMS as $idx => $child_item)
			{
				$child_item->numerate();
			}
		}
		
		function get_numerator()
		{
			return $this->numerator_obj;
		}
		
		function next()
		{
			return $this->brother($this->index_in_parent+1);
		}
		
		function prev()
		{
			return $this->brother($this->index_in_parent-1);
		}
			
		function get_child($idx)
		{
			if($idx<0) return null;
			if($idx>=count($this->_ITEMS)) return null;
			return $this->_ITEMS[$idx];
		}		
		
		function brother($idx)
		{
			if($this->_PARENT==null)
				return null;
			return $this->_PARENT->get_child($idx);
		}
	
		
	}

}