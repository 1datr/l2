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
	
	class MSLayer
	{
		VAR $points=[];
		VAR $parent_mstr=NULL;
		
		function __construct($parent,$regexp_res)
		{
			$this->parent_mstr = $parent;
			$this->regexp_data = $r_e_data;
			foreach($regexp_res as $rr)			
			{				
				$this->points[] = new MSLMarker($rr[0][1], 'start',$rr);
				$start_marker_idx = count($this->points)-1;				
				$this->points[] = new MSLMarker($rr[0][1]+strlen($rr[0][0]), 'end',$rr);
				$end_marker_idx = count($this->points)-1;
				$this->points[$start_marker_idx]->set_end($this->points[$end_marker_idx]);
				$this->points[$end_marker_idx]->set_end($this->points[$start_marker_idx]);
			}
		}
		
		function points()
		{
			return $this->points;
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
			$regexp_res = [];
			preg_match_all($regexp,$this->content,$regexp_res,PREG_OFFSET_CAPTURE|PREG_SET_ORDER);
		//	print_r($regexp_res);
			$this->layers[$lname]=new MSLayer($this,$regexp_res);
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
			
			$the_str = substr($this->content,$pos,$length);
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