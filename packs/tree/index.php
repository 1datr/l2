<?php
namespace tree{
	
	
	// общий класс узел дерева	самый базовый класс. Для всех деревьев
	class tree_node
	{
		VAR $_ITEMS=[];
		VAR $_STANDSTILL=FALSE; // тупиковая ветвь
		VAR $number=NULL;
		VAR $numerator_obj;
		
	
		function add_item(&$item)
		{
		//	print_r($this->numerator_obj);
			
		/*	if(count($this->_ITEMS)>0)
			{
				$obj_num = clone $this->_ITEMS[count($this->_ITEMS)-1]->numerator_obj;
				$obj_num->inc();
			}
			else
			{
				if(!empty($this->numerator_obj))
				{
					$obj_num = clone $this->numerator_obj;
					$obj_num->down();
				}	
			}
	
			if(!empty($obj_num))
			{
				$item->numerator_obj = $obj_num;
				$item->number = $obj_num->getText();
			}*/
			$this->_ITEMS[]=$item;
			$item->_PARENT=$this;
		}
	
	
	}

}