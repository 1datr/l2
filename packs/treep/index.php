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
			return $this->_build_tree($ms_code);
			
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
		
		private function _build_tree($ms_code)
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
			foreach($ms_code->getLayer('comments')->points('start') as $idx => $p)
			{
				$_points[$p->position]=['point'=>$p,'layer'=>'comm_start','idx'=>$idx,];
			}
			foreach($ms_code->getLayer('comments')->points('end') as $idx => $p)
			{
				$_points[$p->position]=['point'=>$p,'layer'=>'comm_end','idx'=>$idx,];
			}
			
			ksort($_points);
			
			$_root = new tn_object();
			$_curr_node = $_root;
			$pos_last=0;
			$p_last=null;
			
			$points_and_strings=[];
			$pos_end=0;
			foreach ($_points as $_pos => $p)
			{
				//if(!is_numeric($pos_last))
				if($p_last!=null)
				{
					$pos_last=$p_last['point']->position;
					//echo "=$pos_last=";
				}
				
				//echo "[".$ms_code->getPositionCoords($pos_last)."]";
				
				if($p['point']->position==0)
				{
					$_substr = new MString('');
					//continue;
				}
				else
					//$_substr = $ms_code->strbetween($pos_end,$p['point']->position-1);
					$_substr = $ms_code->strbetween($pos_last,$p['point']->position-1);
				
				switch($p['layer'])
				{
					case 'start': {
						if($_substr->length()>0)
						{
							$points_and_strings[]=['layer'=>'code','str'=>$_substr];
							
						}
						$points_and_strings[]=$p;
						$pos_end = $p['point']->pos_end+1;
						
						echo "[".$ms_code->getPositionCoords($pos_end)."]";
						
						
						$p_last=$p;
					};break;
					case 'end': {
						
						if($_substr->length()>0)
						{
							$points_and_strings[]=['layer'=>'code','str'=>$_substr];
						}
						$points_and_strings[]=$p;
						
						$pos_end = $p['point']->pos_end+1;						
						echo "[".$ms_code->getPositionCoords($pos_end)."]";
						
						$p_last=$p;
					};break;
					case 'comm_start': {						
						if($_substr->length()>0)
						{
							$points_and_strings[]=['layer'=>'code','str'=>$_substr];
							$p_last=$p;
						}
						$pos_end = $p['point']->pos_end+1;
						
						echo "[".$ms_code->getPositionCoords($pos_end)."]";
					};break;
					case 'comm_end': {					
						if($_substr->length()>0)
						{
							
							$points_and_strings[]=['layer'=>'comment','str'=>$_substr];
							$p_last=$p;
						}
						$pos_end = $p['point']->pos_end+1;						
						echo "[".$ms_code->getPositionCoords($pos_end)."]";
					};break;
				}
				
				
			}
			
			
			foreach($points_and_strings as $_pos => $p)
			{
					
				switch($p['layer'])
				{
					case 'start': {									
						$new_node = new  tn_object();
						//the1utils\utils::mul_dbg($p['point']->regexp_data);
						$new_node->_START_TAG_REGEXP_RESULT = $p['point']->regexp_data;
						$new_node->_END_TAG_REGEXP_RESULT = $p['point']->M_END->regexp_data;
						$_curr_node->add_item($new_node);
						$_curr_node = $new_node;
					};break;
					case 'end': {						
						$_curr_node->add_item($text_node);						
						$_curr_node = $_curr_node->_PARENT;
					};break;
					case 'code': {
						$text_node = new tn_text($p['str']->content);
						$_curr_node->add_item($text_node);
						
					};break;
					case 'comment': {
						$comm_node = new tn_comment($p['str']->content);
						$_curr_node->add_item($comm_node);						
					};break;
				}
				
			}
			
			return $_root;
			
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