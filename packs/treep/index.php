<?php
/*
 ������������� ����������� ������

����������� ����� ����
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
 
� ����������� ��������� �� tnode ������ �� ���������� � ��� ��� �������� ��������� � �������� ���, ����������� � ������������ ������������������, ��� �����
�������������� ��� ������ �����, �������� �� ��������� ������� � ��� ��������� ���� �������� �����.

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

		// ����� ������ ����� ��������� �������� ��������
		public function get_error()
		{
			return $this->ERROR_NO;
		}

		// ����� ��������� ������
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
		// ������� ����������� �� ������ ���� �����
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
		// ������� ����������� �� ������ ���� �����
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
								
							// ��������� �� �������� �� ������� ��� � ���� �����-������ �� ��� ������������
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
				
			// ��������� �������
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
			// ������� �����, ����������� � �������������� ��������
			$to_delete=[];
			foreach($pointbuf as $str => $info)
			{
				foreach($map as $map_item)
				{
					if(($map_item['start']<=$str)&&($str<=$map_item['end']))	// �������� � ������������ ������
					{
						$to_delete[]=$str;	// ������� � ��������� � ���������
						break;
					}
				}
			}
			foreach ($to_delete as $_str)
			{
				unset($pointbuf[$_str]);
			}
		}
		/* ��������������� � ������
		 $params - ������������� ������ ���������� �� ���������� �������
		 ��������� :
		 code - ��������������� ������ ����
		 nstart - ���������� ��������� ��������� �������
		 nend  - ���������� ��������� �������� �������
		 comments - ���������� ��������� ������ ������������ (������������ � �������������) ���������� ������ �����
		 shields - ������ ������������ ������������������� [���. ���. ������, ���. ���. �����]
		 ���������-������� :
		 onmapready($pointbuf) - ����� ���������� �����
		 onnoderady($curr_node) - ����� ���������� ����

		 * */
		public function compile($params)
		{
			the1utils\utils::def_options(['comments'=>['#\/\*.*\*\/#Uis','#\/\/.*$#m'],
					'delete_comments'=>true,
			],$params);
				
			$ms_code = new MString($params['code']);
			$ms_code->addLayer('comments', $params['comments']);
			foreach($ms_code->getLayer('comments')->points() as $p)
			{
				echo " ".$p->position;
			}
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