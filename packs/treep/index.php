<?php
/*
 Универсальный древовидный парсер

Преобразует текст типа
/# {#rz
#/
{@
{#reef  #}
@}

xxx
{#if c=\"xx\" (x==0)
{#then
x=x+1;
#}
{#else
	x=x+2;
#}
// #}
#}
xxx
{#foreach(arr_x as idx => x)

	#}
ddd
 
в древовидную структуру из tnode исходя из соглашения о том как выглядит начальный и конечный тэг, комментарии и экранирующие последовательности, где текст
воспринимается как единый текст, несмотря на возможное наличие в нем начальных либо конечных тегов.

* */
namespace treep{
	
	use the1utils;
use the1utils\MString;
		
	require_once __DIR__.'/lib/nodes.php';

	class TreeP
	{

		VAR $ERROR_NO = 0;
		var $ERROR_TEXTS = [1=>'Parse error'];
		VAR $_COMMENTS_MAP=[];

		// номер ошибки после последней операции парсинга
		public function get_error()
		{
			return $this->ERROR_NO;
		}

		// текст последней ошибки
		public function get_err_text()
		{
			if(isset($this->ERROR_TEXTS[$this->ERROR_NO]))
			{
				return $this->ERROR_TEXTS[$this->ERROR_NO];
			}
			return "";
		}

		public function required()
		{
			return ['base.tree'];
		}


		private function calc_open_close($buf,&$count_open,&$count_closed)
		{
			$count_open=0;
			$count_closed=0;
			foreach($buf as $str => $point)
			{
				if($point['type']=='open')
				{
					$count_open++;
				}
				elseif($point['type']=='closed')
				{
					$count_closed++;
				}
			}
		}
		// удалить комментарии из строки если нужно
		private function clear_comments2($params,&$the_str)
		{
			if($params['delete_comments'])
			{
				if(is_array($params['comments']))
				{
					foreach ($params['comments'] as $idx => $_str)
					{
						$the_str = preg_replace($_str, "", $the_str);
					}
				}
				elseif(is_string($params['comments']))
				{
					$the_str = preg_replace($params['comments'], "", $the_str);
				}
			}
		}
		// удалить комментарии из строки если нужно
		private function clear_comments($params,&$the_str)
		{
			if($params['delete_comments'])
			{
				if(is_array($params['comments']))
				{
					foreach ($params['comments'] as $idx => $_str)
					{
						$the_str = preg_replace($_str, "", $the_str);
					}
				}
				elseif(is_string($params['comments']))
				{
					$the_str = preg_replace($params['comments'], "", $the_str);
				}
			}
		}

		private function make_comments_map($params)
		{
			$_COMMENTS_MAP=[];
			if(is_array($params['comments']))
			{
				foreach ($params['comments'] as $idx => $_str)
				{
					$_matches=[];
					preg_match_all($_str, $params['code'],$_matches, PREG_OFFSET_CAPTURE);							
					
					foreach($_matches[0] as $_mt)
					{
					//	
						//\the1utils\utils::mul_dbg($_mt);
						$_COMMENTS_MAP[]=['start'=>$_mt[1],
								'end'=>$_mt[1]+strlen($_mt[0]),
								'code'=>$_mt[0]
						];
					}
				}
			}
			elseif(is_string($params['comments']))
			{
				$_matches=[];
				preg_match_all($_str, $params['code'],$_matches, PREG_OFFSET_CAPTURE);

				foreach($_matches as $_mt)
				{
				//	\the1utils\utils::mul_dbg($_mt);
					$_COMMENTS_MAP[]=['start'=>$_mt[0][1],
							'end'=>$_mt[0][1]+strlen($_mt[0][0]),
							'code'=>$_mt[0][0]
					];
				}
			}
			
				
			return $_COMMENTS_MAP;
		}

		public function AfterLoad()
		{
			$this->load_lib('nodes');
		}

		private function get_shields_areas($params)
		{
			$shields = [];
			if(isset($params['shields']))
			{
				if(is_array($params['shields']))
				{
					$ptrn='/';
					foreach($params['shields'] as $shidx => $shld)
					{
						$ptrn = '/'.$shld[0].'(.*)'.$shld[1].'/Us';
							
						$_shields=[];
						preg_match_all($ptrn, $params['code'],$_shields, PREG_OFFSET_CAPTURE);
						foreach ($_shields[0] as $_shld)
						{
							$_code = $_shld[0];
								
							// проверяем не содержит ли текущий код в себе какой-нибудь из уже существующих
							/*
							 foreach ($shields as $shld_item)
							 {

							 }*/
								
							$shields[]=['start'=>$_shld[1],
									'end'=>$_shld[1]+strlen($_shld[0]),
									'code'=>$_code
							];
						}
					}
				}
			}
				
			// фильтруем регионы
			$idx_to_unset=[];
			foreach ($shields as $idx1 => $shld1)
			{
				foreach ($shields as $idx2 => $shld2)
				{
					if( ($idx1!=$idx2)&&(!in_array($idx1, $idx_to_unset)) )
					{
						if(($shld1['start']<=$shld2['start'])&&($shld2['end']<=$shld1['end']))
						{
							$idx_to_unset[]=$idx1;
							break;
						}
					}
				}
			}
			foreach ($idx_to_unset as $_idx)
			{
				unset($shields[$_idx]);
			}
			return $shields;
		}

		private function delete_shilds($params,&$str)
		{
			if(isset($params['shields']))
			{
				if(is_array($params['shields']))
				{
					foreach($params['shields'] as $shidx => $shld)
					{
						the1utils\utils::def_options(['clear'=>true], $shld);
						if($shld['clear'])
						{
							$_shields=[];
							$ptrn = '/'.$shld[0].'(.*)'.$shld[1].'/sm';
							$str = preg_replace($ptrn, '$1', $str);
						}
					}
				}
			}
		}

		private function filter_by_map($map,&$pointbuf)
		{
			// убираем точки, оказавшиеся в экранированных регионах
			$to_delete=[];
			foreach($pointbuf as $str => $info)
			{
				foreach($map as $map_item)
				{
					if(($map_item['start']<=$str)&&($str<=$map_item['end']))	// попадает в экранируемый регион
					{
						$to_delete[]=$str;	// удаляем и переходим к следующей
						break;
					}
				}
			}
			foreach ($to_delete as $_str)
			{
				unset($pointbuf[$_str]);
			}
		}
		/* откомпилировать в дерево
		 $params - ассоциативный массив параметров со строковыми ключами
		 Параметры :
		 code - непосредственно строка кода
		 nstart - регулярное выражение стартовых токенов
		 nend  - регулярное выражение конечных токенов
		 comments - регулярное выращение блоков комментариев (однострочных и многострочных) строкалибо массив строк
		 shields - массив экранирующих последовательностей [рег. выр. начало, рег. выр. конец]
		 Параметры-события :
		 onmapready($pointbuf) - после построения карты
		 onnoderady($curr_node) - после построения узла

		 * */
		public function compile($params)
		{
			the1utils\utils::def_options(['comments'=>['#\/\*.*\*\/#Uis','#\/\/.*$#m'],
					'delete_comments'=>true,
			],$params);
				
			$ms_code = new MString($params['code']);
			$ms_code->addLayer('comments', $params['comments']);
			$ms_code->addLayer('shields', $params['shields']);
			$ms_code->addLayer('nstart', $params['nstart']);
			$ms_code->addLayer('nend', $params['nend']);
			$ms_code->eat('shields', 'comments');
			$ms_code->eat('shields', 'nstart');
			$ms_code->eat('shields', 'nend');
			$ms_code->eat('comments', 'nstart');
			$ms_code->eat('comments', 'nend');
		/*	foreach($ms_code->getLayer('nstart')->points() as $p)
			{
				echo " ".$p->position;
			}*/
			
			
			$numerator = new \hnumerator\HNnumerator();
			$node_root = new tn_object(true);
			$node_root->number = $numerator->getText();
			$node_root->numerator_obj = $numerator;
			
			
			$curr_node = $node_root;
			$start_nodes = $ms_code->getLayer('nstart')->points();
			$end_nodes = $ms_code->getLayer('nend')->points();
			
		//	$juxtaposed = $ms_code->juxtapose('nstart','nend');
			if(count($start_nodes)!=count($end_nodes))
			{
				return ['error'=>'Parse error '];
			}
			
			//$res = $this->get_brother_node($ms_code,$curr_node);
			$pos_start = 0;
			$pos_end = 0;
			$binded=[];
			return $this->build_tree_exec($ms_code);
			
			//$this->bind_pairs($ms_code,$curr_node,0,$binded);
			/*
			foreach($binded as $idx => $bnd)
			{
				echo "\n $idx : ";
				//echo $bnd['start']->regexp_data[0][1];
				echo "\n start : ".$bnd['start']->_LAYER->parent_mstr->getPositionCoords($bnd['start']->regexp_data[0][1])."\n";
				print_r($bnd['start']->regexp_data[0][0]);
				echo "\n end : ".$bnd['end']->_LAYER->parent_mstr->getPositionCoords($bnd['end']->regexp_data[0][1])."\n";
				print_r($bnd['end']->regexp_data[0][0]);
			}
			*/
		
			return $curr_node;
			
		}
		
		private function build_tree_exec($ms_code)
		{
			$str_to_eval='$root = $this->build_node($ms_code,['."\n\r";
			$last_p=0;
			$last_p_pos=0;
			
			$_points=[];
			foreach($ms_code->getLayer('nstart')->points('start') as $idx => $p)
			{
				$_points[$p->position]=['point'=>$p,'layer'=>'start','idx'=>$idx,];
			}
			foreach($ms_code->getLayer('nend')->points('start') as $idx => $p)
			{
				$_points[$p->position]=['point'=>$p,'layer'=>'end','idx'=>$idx,];
			}
			
			ksort($_points);
						
			foreach($_points as $idx => $pt)
			{
				$p=$pt['point'];
				$pidx=$pt['idx'];
				if(!is_numeric($last_p))
				{
					$last_p_pos = $last_p->pos_end;
				}//"\n\r\t"
				$str_to_eval = $str_to_eval.'$ms_code->strbetween('.$last_p_pos.','.($p->position-1).'),';
				if($pt['layer']=='start')
					$str_to_eval = $str_to_eval.'$this->build_node($ms_code,[$ms_code->getLayer("nstart")->points("start")['.$pidx.'],';
				else
					$str_to_eval = $str_to_eval.']),';
				$last_p=$p;
			}
			if($pt['point']->position<strlen($ms_code->content)-1)
			{
				$last_p_pos = $pt['point']->pos_end;
				$str_to_eval = $str_to_eval.'$ms_code->strbetween('.$last_p_pos.','.(strlen($ms_code->content)-1).'),';
			}
			$str_to_eval=$str_to_eval.']);'."\n\r";
		//	echo $str_to_eval;
			 eval($str_to_eval);
			return $root;
		}
		
		private function build_node($mstr,$items)
		{
			$_node = new tn_object();
			if(the1utils\utils::is_object_of($items[0],"the1utils\MSLMarker"))
			{
				$node_item = $items[0];
				unset($items[0]);
				$_node->_START_TAG_REGEXP_RESULT=$node_item->regexp_data;
				$_node->_END_TAG_REGEXP_RESULT=$node_item->M_END->regexp_data;
			}
			foreach($items as $idx => $item)
			{
				//the1utils\utils::mul_dbg(get_class($item));
				if(the1utils\utils::is_object_of($item, 'treep\tn_object'))
				{
					$_node->add_item($item);									
				}
				elseif(\the1utils\utils::is_object_of($item,"the1utils\MString")) 
				{
					$splitted = $item->split_by_layer('comments');
					//print_r($item->layers['comments']->points());
					
					foreach($splitted as $j => $spl_item)
					{
						if($spl_item['in_layer'])
						{
							$comment_node = new tn_comment($spl_item['str']);
							//the1utils\utils::mul_dbg($txt_node);
							$_node->add_item($comment_node);
						}
						else 
						{
							$txt_node = new tn_text($spl_item['str']);
							//the1utils\utils::mul_dbg($txt_node);
							$_node->add_item($txt_node);
						}
					}
					
					//the1utils\utils::mul_dbg($item->content);
					//echo "\n>> ".$item->content;
				}
			}
			return $_node;
		}
		
		private function bind_pairs($ms_code,&$curr_node,$pos_start,&$binded,$lastpoint=0)
		{					
			$go_recursive = 0;
			$next_start=true;
			//	echo "=$mpos=";
			$curr_marker = $ms_code->getLayer('nstart')->points()[$pos_start];
			
		//	the1utils\utils::mul_dbg($pos_start." >> ".$curr_marker->regexp_data[0][0]);
			
			$next_on_end = $ms_code->find_closest_in_layer($ms_code->getLayer('nstart')->points()[$pos_start]->pos_end ,'nend');
			$next_on_start = $ms_code->find_closest_in_layer($ms_code->getLayer('nstart')->points()[$pos_start]->pos_end ,'nstart');
			
			if($next_on_start==null) 
			{
				$go_recursive = 0;	
				$next_start=false;
			}
			else 
			{
				if($next_on_start->position<$next_on_end->position)
				{
					$go_recursive = 1;
				}
				else 
				{
					$go_recursive = 0;
				}
			}
			
			if($go_recursive)
			{		 
				$meat_str1 = $ms_code->strbetween($curr_marker->pos_end+1,$next_on_start->position-1);
			
				$this->add_meat($curr_node,$meat_str1);
				// движемся к следующей
				$this->bind_pairs($ms_code,$curr_node,$pos_start+2,$binded,$curr_marker);
				
				\the1utils\utils::mul_dbg( \the1utils\utils::grub_code(function() use ($binded,$ms_code)
				{
					foreach ($binded as $b)
					{
						echo "\nstart=".$ms_code->getPositionCoords($b['start']->position)." ";
						echo "end=".$ms_code->getPositionCoords($b['end']->position);
						echo "";
					}	
				}) );
				
				$lastpoint = $binded[count($binded)-1]['nend'];
				
				$next_on_end = $ms_code->find_closest_in_layer($binded[count($binded)-1]->position,'nend');
				$binded[]=['start'=>$curr_marker,'end'=>$next_on_end];
				
			//	echo "==".$ms_code->getPositionCoords($lastpoint->pos_end);
				
				$meat_str2=$ms_code->strbetween($lastpoint->pos_end+1,$next_on_end->position-1);
			//	echo "\n>>".$meat_str2;
				
		//		$this->add_meat($curr_node,$meat_str);
			
				// add node
				$newnode = $this->add_obj_node($curr_marker,$next_on_end,$curr_node);
				
				//$this->add_meat($newnode,$meat_str2);
								
				
				//$curr_node->add_item($newnode);
			}
			else 
			{
				$binded[]=['start'=>$curr_marker,'end'=>$next_on_end];
				
				
			//	$meat_str=$ms_code->substr($lastpoint,$curr_marker);
				
			//	echo ">>".$meat_str;
				
		//		$this->add_meat($curr_node,$meat_str);
			
				// add node
				$newnode = $this->add_obj_node($curr_marker,$next_on_end,$curr_node);
				
				//$curr_node->add_item($newnode);
				
				if($next_start)
				{
					$this->bind_pairs($ms_code,$curr_node,$pos_start+2,$binded,$next_on_end);
				}
			}
		}
		
		private function add_meat($curr_node,$m_str_code)
		{
			$splitted = $m_str_code->split_by_layer('comments');
			//echo "\n>> ".$m_str_code;
			//print_r($splitted);
			foreach($splitted as $item)
			{
				if($item['in_layer'])
				{
					$meat_node = new tn_comment($item['str']);
					$curr_node->add_item($meat_node);
				}
				else 
				{
					$meat_node = new tn_text($item['str']);
					$curr_node->add_item($meat_node);
				}
			}
		}
		
		private function add_obj_node($start_marker,$end_marker,$curr_node)
		{
			$newnode = new tn_object();
			$newnode->_START_TAG_REGEXP_RESULT = $start_marker->regexp_data;
			$newnode->_END_TAG_REGEXP_RESULT = $end_marker->regexp_data;
			$newnode->_PARENT = $curr_node;
			$curr_node->add_item($newnode);
			return $newnode;
		}
				
		private function detect_pieces_and_insert($_node_str,$params,$the_node)
		{
			$params2=$params;
			$params2['code']=$_node_str;
			
			$comments_map = $this->make_comments_map($params2);
			
			$_shields = $this->get_shields_areas($params2);
			foreach($comments_map as $comment)
			{
				
			}
		}

	}

}