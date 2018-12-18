<?php
namespace the1utils
{
	class MSLMarker
	{
		VAR $type;
		VAR $position;
		VAR $M_BEGIN;
		VAR $M_END;
		VAR $regexp_data=NULL;
		function __construct($xpos,$type,$r_e_data)
		{
			$this->position = $xpos;
			$this->type = $type;
			$this->regexp_data = $r_e_data;
			switch($this->type)
			{
				case 'start': 
						$this->M_BEGIN=$this;
					break;
				case 'end': 
						$this->M_END=$this;
					break;
			}
		}
		
		function set_begin($m_beg)
		{
			$this->M_BEGIN=$m_beg;
		}
		
		function set_end($m_end)
		{
			$this->M_END=$m_end;
		}
	}
	// layer of markers
	class MSLayer
	{
		VAR $points=[];
		VAR $parent_mstr=NULL;
		
		function __construct($parent,$regexp_res)
		{
			$this->parent_mstr = $parent;
			$this->regexp_data = $r_e_data;
			$this->points = [];
			$this->add_points($regexp_res);
			/*foreach($regexp_res as $rr)			
			{				
				$this->points[] = new MSLMarker($rr[0][1], 'start',$rr);
				$start_marker_idx = count($this->points)-1;				
				$this->points[] = new MSLMarker($rr[0][1]+strlen($rr[0][0])-1, 'end',$rr);
				$end_marker_idx = count($this->points)-1;
				$this->points[$start_marker_idx]->set_end($this->points[$end_marker_idx]);
				$this->points[$end_marker_idx]->set_end($this->points[$start_marker_idx]);
			}*/
		}
		
		function add_points($_points)
		{
			foreach ($_points as $rr)
			{
				$this->points[] = new MSLMarker($rr[0][1], 'start',$rr);
				$start_marker_idx = count($this->points)-1;
				$this->points[] = new MSLMarker($rr[0][1]+strlen($rr[0][0])-1, 'end',$rr);
				$end_marker_idx = count($this->points)-1;
				$this->points[$start_marker_idx]->set_end($this->points[$end_marker_idx]);
				$this->points[$end_marker_idx]->set_end($this->points[$start_marker_idx]);
			}
		}
		
		function points()
		{
			return $this->points;
		}	
		// get markers between pos1 and pos2
		function get_markers($pos1=-1,$pos2=-1)
		{
			if($pos2<0)
				$pos2 = count($this->points);
			$res=[];
			//print_r([$pos1,$pos2]);
			foreach($this->points as $idx=> $p)
			{				
				if(($pos1<=$p->position)&&($p->position<=$pos2))
				{
					$res[]=$idx;
				}
			}
			return $res;
		}
		
		function delete($pos)
		{
			unset($this->points[$pos]);
		}
		
		function cut_n_move($pos1,$pos2)
		{
			foreach($this->points as $idx => $p)
			{
			//	echo "$pos1 :: $pos2";
				if(($p->position<$pos1)||($p->position>$pos2))
				{
					unset($this->points[$idx]);
				}
				else 
				{
					
					$p->position-=$pos1;
				}
			}
		}
	}
	
	class MString
	{
		VAR $content;
		VAR $layers=[];
		function __construct($str='')
		{
			$this->content = $str;
		}
		
		function Layers()
		{
			return $this->layers;
		}
		
		function getLayer($lname)
		{
			return $this->layers[$lname];
		}
		
		function addLayer($lname,$regexp)
		{
			if(is_array($regexp))
			{
				foreach ($regexp as $re)
				{
					$this->add_to_layer($lname,$re);					
				}
			}
			else 
				$this->add_to_layer($lname,$regexp);
		}
		
		function add_to_layer($lname,$_regexp)
		{
			if(empty($this->layers[$lname]))
			{
				$regexp_res = [];
				preg_match_all($_regexp,$this->content,$regexp_res,PREG_OFFSET_CAPTURE|PREG_SET_ORDER);
				$this->layers[$lname]=new MSLayer($this,$regexp_res);
			}
			else 
			{
				$regexp_res = [];
				preg_match_all($_regexp,$this->content,$regexp_res,PREG_OFFSET_CAPTURE|PREG_SET_ORDER);
				$this->layers[$lname]->add_points($regexp_res);
			}
		}
		
		function eat($l_volk,$l_zayac)
		{
			foreach($this->layers[$l_volk]->points() as $p_start)
			{
				//print_r($p_start->type);
				if($p_start->type=='start')
				{
					$p_end = $p_start->M_END;
				//	print_r([$p_start->position,$p_end->position]);
					$pos_between = $this->layers[$l_zayac]->get_markers($p_start->position,$p_end->position);
				//	print_r($pos_between);
					foreach($pos_between as $pos)
					{
						$this->layers[$l_zayac]->delete($pos);
					}
				}
			}
		}
		
		function substr($pos,$length)
		{
			//print_r($pos);
			//print_r($length);
			if(\the1utils\utils::is_object_of($pos,"the1utils\MSLMarker"))
			{
				$pos = $pos->position;
			}
			
			if(\the1utils\utils::is_object_of($length,"the1utils\MSLMarker"))
			{
				$pos2 = $length->position;				
				$length = $pos2-$pos;
			}
			
			$the_str = mb_substr($this->content,$pos,$length);
			if($pos<0)
				$pos1 = strlen($this->content)+$pos;
			else 
				$pos1 = $pos;
			if($length<0)
				$pos2 = strlen($this->content)+$length;
			else 
				$pos2 = $pos1+$length;
		
			$newstr = new MString($the_str);
			$newstr->layers = $this->layers;
			foreach($newstr->Layers() as $lname => $layer)
			{
				//print_r($layer);
				$newstr->getLayer($lname)->cut_n_move($pos1,$pos2);
			}
			
			return $newstr;
		}
	}
}