<?php
namespace phpjs{
	
	use treep\TreeP ;
	use hnumerator\HNnumerator;
use treep\tn_object;
use treep\tn_text;
use treep\tn_comment as tn_comment;
use the1utils\utils;
				
	// общий класс узел дерева	самый базовый класс. Для всех деревьев
	class phpjs
	{
		
		static function compile($code)
		{
			$_treep = new \treep\TreeP();
			$compiled = $_treep->compile([
					'code'=>$code,//file_get_contents('./example.js'),
					'nstart'=>'/((while|for|foreach|if|elseif|switch|try|catch|finally)\((.+)\).*$\s*\{)|((while|for|foreach|if|elseif|switch)\((.+)\).*\s*\{)|(else\s*\{)/',
					'nend'=>'#}#',
					'comments'=>['#/\*.*\*/#Uis','#\/\/.*$#m'],
					'shields'=>['#\\".*\\"#Uis',"#'.*'#Uis",],
			]);
			
			$root=new tn_object();
			$curr_node=$root;
			
			$compiled->walk(function($item) use (&$curr_node)
			{
				if($item->_PARENT==null) return;
				if(!empty($item->_START_TAG_REGEXP_RESULT))
				{
				//	echo "[".$item->number."]<+".$item->_START_TAG_REGEXP_RESULT[0][0]."+>";
					$cloned = clone $item;
					$cloned->clear_items();
					$curr_node->add_item($cloned);
					$curr_node = $cloned;
				}
				elseif(utils::is_object_of($item, 'tn_comment'))
				{
					$curr_node->add_item($item);
				}
				else
				{
					
					$exploded = explode(';',$item->_TEXT);
				//	print_r($exploded);
					foreach($exploded as $ex)
					{
						if(empty($ex))
							continue;
						$tn =new tn_text($ex);
						$curr_node->add_item($tn);
					}
				}
					//echo "\n\r[".$item->number."][".$item->_TEXT."]";
			});
			
			$root->numerate();
			return $root;
			
		//	return $compiled;
		} 
		
	}

}