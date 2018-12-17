<?php
namespace treep{
	
	/*
	 Элемент дерева, строимого парсером
	 */
	class treep_node extends \tree\tree_node 
	{
	
		VAR $_IS_TEXT=FALSE;
	
		function is_text()
		{
			return $this->_IS_TEXT;
		}
	}
	/* текстовый узел */
	class tn_text extends treep_node
	{
		VAR $_TEXT;
	
		function __construct($_text_)
		{
			$this->_STANDSTILL = true;
			$this->_TEXT=$_text_;
			$this->_IS_TEXT = true;
		}
	
		function text()
		{
			return $this->_TEXT;
		}
			
	}
	
	
	class tn_object extends treep_node
	{
	
		VAR $_START_TAG_REGEXP_RESULT=[];
		VAR $_END_TAG_REGEXP_RESULT=[];
		VAR $ROOT=false;
		VAR $_POS_STRAT=0;
		VAR $_POS_START_END=0;
		VAR $_POS_END=0;
		VAR $_POS_END_END=0;
		VAR $_PARENT=null;
	
	
	
		function __construct($root=false)
		{
			$this->ROOT = $root;
		}
	
		function add_strat_regexp($arr)
		{
			$this->_START_TAG_REGEXP_RESULT[]=$arr;
		}
	
		function add_end_regexp($arr)
		{
			$this->_START_TAG_REGEXP_RESULT[]=$arr;
		}
	
		function close()
		{
				
		}
		// ходим по дереву, выполняя на каждом узле процедуру вида function($node){}
		function walk($onwalk_callback)
		{
			$onwalk_callback($this);
			foreach($this->_ITEMS as $idx => $item)
			{
				if(is_object($item))
				{
					if(get_class($item)==get_class($this))
					{
						$item->walk($onwalk_callback);
					}
					else
					{
						$onwalk_callback($item);
					}
				}
				else
				{
					$onwalk_callback($item);
				}
	
			}
		}
		
	}

}