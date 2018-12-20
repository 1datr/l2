<?php
namespace the1utils
{
	class MSLMarker
	{
		VAR $type;
		VAR $position;
		VAR $pos_end;
		VAR $M_BEGIN;
		VAR $M_END;
		VAR $_LAYER;		
		VAR $regexp_data=NULL;
		VAR $brother=NULL;
		function __construct($xpos,$type,$r_e_data,$_L)
		{
			$this->position = $xpos;
			$this->_LAYER=$_L;
			$this->pos_end = $xpos+strlen($r_e_data[0][0])-1;
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
				
		function contains($_pos)
		{
			//if($this.position<=$pos)&&($this->M_END.position)
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
		}	
				
		function add_points($_points)
		{
			foreach ($_points as $rr)
			{
				$this->points[] = new MSLMarker($rr[0][1], 'start',$rr,$this);
				$start_marker_idx = count($this->points)-1;
				$this->points[] = new MSLMarker($rr[0][1]+strlen($rr[0][0])-1, 'end',$rr,$this);
				$end_marker_idx = count($this->points)-1;
				$this->points[$start_marker_idx]->set_end($this->points[$end_marker_idx]);
				$this->points[$end_marker_idx]->set_end($this->points[$start_marker_idx]);
			}
		}
		
		function in_layer($pos)
		{
			foreach($this->points as $idx=> $p)
			{
				if($p->contains($pos))
					return $p;
			}
			return null;
		}
		
		function points($type=NULL)
		{
			if($type==NULL)
				return $this->points;
			else 
			{
				$plist=[];
				foreach($this->points as $idx => $p)
				{
					if($p->type==$type)
						$plist[]=$p;
				}
				return $plist;
			}
			return null;
		}	
		// get markers between pos1 and pos2
		public function get_markers($pos1=-1,$pos2=-1)
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
	
	class StrPosXY
	{
		VAR $x;
		VAR $y;
		
		function __toString()
		{
			return $this->y." : ".$this->x;
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
		
		function __toString()
		{
			return $this->content;
		}
		
		function split_by_layer($spl)
		{
			$last_point=0;
			$res=[];
			foreach($this->layers[$spl]->points() as $idx => $point)
			{
				$inlayer=false;
				if(is_numeric($last_point))
				{
					$inlayer=false;
					$ss = $this->substr($last_point, $point->position)->content;
				}
				else 
				{
					if(($last_point->type=='start')&&($point->type=='end'))
					{
						$inlayer=true;	
						$ss = $this->substr($last_point, $point)->content;
					}
					else 
						$ss = $this->substr($last_point->pos_end, $point->position)->content;
				}
				if(!empty($ss))
				{
					$res[]=['str'=>$ss,'in_layer'=>$inlayer];					
				}
				$last_point = $point;
			}
			
			if($point->pos_end<strlen($this->content)-1)
			{
				$ss = $this->substr($point)->content;
				if(!empty($ss))
					$res[]=['str'=>$ss,'in_layer'=>false];
			}
			return $res;
		}
		
		function getPositionCoords($pos,$no_zero=true)
		{
			$res = new StrPosXY();
			$res->y=0;
			for($i=0;$i<$pos;$i++)
			{
				if($i>=strlen($this->content)) return null;
			//	echo substr($this->content,$i,1);
				if(substr($this->content,$i,1)=="\n")
				{
					$res->y++;
					$res->x=0;
				}
				elseif(substr($this->content,$i,1)=="\t")
				{
					$res->x++;
				}
				else 
				{
					$res->x++;
				}
			}
			if($no_zero)
			{
				$res->x++;
				$res->y++;
			}
			return $res;
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
				//\the1utils\utils::mul_dbg($regexp_res);
				$this->layers[$lname]=new MSLayer($this,$regexp_res);
			}
			else 
			{
				$regexp_res = [];
				preg_match_all($_regexp,$this->content,$regexp_res,PREG_OFFSET_CAPTURE|PREG_SET_ORDER);
			//	\the1utils\utils::mul_dbg($regexp_res);
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
					$this->layers[$l_zayac]->points = \the1utils\utils::rebuild_array($this->layers[$l_zayac]->points);
				}
			}
		}
	
		
		public function find_closest_in_layer($pos,$_layer,$_type='start')
		{
			foreach($this->layers[$_layer]->points() as $idx => $p)
			{
				if(($p->position>$pos)&&($p->type==$_type))
					return $p;
			}
			return null;
		}
		
		function strbetween($pos1,$pos2)
		{
			return $this->substr($pos1,$pos2-$pos1+1);
		}
		function substr($pos,$length='end')
		{			
			if(\the1utils\utils::is_object_of($pos,"the1utils\MSLMarker"))
			{
				$pos = $pos->position;
			}
			
			if(\the1utils\utils::is_object_of($length,"the1utils\MSLMarker"))
			{
				$pos2 = $length->pos_end;				
				$length = $pos2-$pos;
			}
			
			if($length=='end')
			{
				$length=strlen($this->content)-$pos-1;				
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
			
			if($length=='end')
			{
				$pos2 = strlen($this->content)-1;
			}
		
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