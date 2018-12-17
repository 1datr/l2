<?php
namespace the1utils
{	

class utils
{

	static function merge_arrays($array1,$array2)
	{
		$res = $array1;
		if(!empty($array2))
		{
			foreach ($array2 as $key => $val)
			{
				if(!in_array($val,$res))
				{
					$res[]=$val;
				}
			}
		}
		return $res;
	}
	
	static function get_basic_url()
	{
		$result = ''; // Р СџР С•Р С”Р В° РЎР‚Р ВµР В·РЎС“Р В»РЎРЉРЎвЂљР В°РЎвЂљ Р С—РЎС“РЎРѓРЎвЂљ
		$default_port = 80; // Р СџР С•РЎР‚РЎвЂљ Р С—Р С•-РЎС“Р С�Р С•Р В»РЎвЂЎР В°Р Р…Р С‘РЎР‹
		 
		  // Р С’ Р Р…Р Вµ Р Р† Р В·Р В°РЎвЂ°Р С‘РЎвЂ°Р ВµР Р…Р Р…Р С•Р С�-Р В»Р С‘ Р С�РЎвЂ№ РЎРѓР С•Р ВµР Т‘Р С‘Р Р…Р ВµР Р…Р С‘Р С‘?
		if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=='on')) {
		    // Р вЂ™ Р В·Р В°РЎвЂ°Р С‘РЎвЂ°Р ВµР Р…Р Р…Р С•Р С�! Р вЂќР С•Р В±Р В°Р Р†Р С‘Р С� Р С—РЎР‚Р С•РЎвЂљР С•Р С”Р С•Р В»...
		    $result .= 'https://';
		    // ...Р С‘ Р С—Р ВµРЎР‚Р ВµР Р…Р В°Р В·Р Р…Р В°РЎвЂЎР С‘Р С� Р В·Р Р…Р В°РЎвЂЎР ВµР Р…Р С‘Р Вµ Р С—Р С•РЎР‚РЎвЂљР В° Р С—Р С•-РЎС“Р С�Р С•Р В»РЎвЂЎР В°Р Р…Р С‘РЎР‹
		    $default_port = 443;
		} else {
		    // Р С›Р В±РЎвЂ№РЎвЂЎР Р…Р С•Р Вµ РЎРѓР С•Р ВµР Т‘Р С‘Р Р…Р ВµР Р…Р С‘Р Вµ, Р С•Р В±РЎвЂ№РЎвЂЎР Р…РЎвЂ№Р в„– Р С—РЎР‚Р С•РЎвЂљР С•Р С”Р С•Р В»
		    $result .= 'http://';
		}
		// Р пїЅР С�РЎРЏ РЎРѓР ВµРЎР‚Р Р†Р ВµРЎР‚Р В°, Р Р…Р В°Р С—РЎР‚. site.com Р С‘Р В»Р С‘ www.site.com
		$result .= $_SERVER['HTTP_HOST'];
		 /*
		// Р С’ Р С—Р С•РЎР‚РЎвЂљ РЎС“ Р Р…Р В°РЎРѓ Р С—Р С•-РЎС“Р С�Р С•Р В»РЎвЂЎР В°Р Р…Р С‘РЎР‹?
		if ($_SERVER['SERVER_PORT'] != $default_port) {
		    // Р вЂўРЎРѓР В»Р С‘ Р Р…Р ВµРЎвЂљ, РЎвЂљР С• Р Т‘Р С•Р В±Р В°Р Р†Р С‘Р С� Р С—Р С•РЎР‚РЎвЂљ Р Р† URL
		   	$result .= ':'.$_SERVER['SERVER_PORT'];
		}
		// Р СџР С•РЎРѓР В»Р ВµР Т‘Р Р…РЎРЏРЎРЏ РЎвЂЎР В°РЎРѓРЎвЂљРЎРЉ Р В·Р В°Р С—РЎР‚Р С•РЎРѓР В° (Р С—РЎС“РЎвЂљРЎРЉ Р С‘ GET-Р С—Р В°РЎР‚Р В°Р С�Р ВµРЎвЂљРЎР‚РЎвЂ№).
		$result .= $_SERVER['REQUEST_URI'];
		// Р Р€РЎвЂћРЎвЂћ, Р Р†РЎР‚Р С•Р Т‘Р Вµ Р С—Р С•Р В»РЎС“РЎвЂЎР С‘Р В»Р С•РЎРѓРЎРЉ!*/
		return $result;
	}
	
	static function sel_if($cond,$if_true="selected",$if_false="")
	{
		return $cond ? $if_true : $if_false;
	}
	
	static function str_insert($str_1, $pos, $str_2)
	{
		return substr($str_1,0,$pos).$str_2.substr($str_1,$pos);
	}
	
	static function replace_substr($_str,$pos1,$pos2,$_replace)
	{
		$str_left='';
		$str_right='';
		
		if($pos1>0) $str_left=substr($_str,0,$pos1);
		if($pos2+1<strlen($_str)-1) $str_right=substr($_str,$pos2+1);
		
		return $str_left.$_replace.$str_right;
	}
	
	static function utf8ize($d) {
	    if (is_array($d))
	        foreach ($d as $k => $v)
	            $d[$k] = utf8ize($v);
	
	     else if(is_object($d))
	        foreach ($d as $k => $v)
	            $d->$k = utf8ize($v);
	
	     else
	        return utf8_encode($d);
	
	    return $d;
	}
	
	static function proc_array($arr,$funct)
	{
			$newarr=[];
			foreach($arr as $key => $val)
			{
					$res = $funct($key,$val);
					$newarr[$key]=$res;
			}
			return $newarr;
	}
	
	static function str_trunc($_str,$_length,$mode='str')
	{
	//	mul_dbg(var_dump([$_str,$_length,$mode]));
		if(strlen($_str)>$_length)
		{
		//	mul_dbg(substr($_str,0,$_length));
			if($mode=='str')
			{
				return mb_substr($_str,0,$_length,'UTF-8')."...";
			}
			elseif($mode=='arr')
			{
				return ['str'=>mb_substr($_str,0,$_length,'UTF-8'),'trunced'=>true];
			}
		}
		else
		{
			if($mode=='str')
			{
				return $_str;
			}
			elseif($mode=='arr')
			{
				return ['str'=>$_str,'trunced'=>false];
			}
		}
	}
	
	static function merge_arrays_assoc()
	{
		$numargs = func_num_args();
		$arg_list = func_get_args();
		$_merge_if_not_exists=true;
		if(is_bool($args_list[$numargs-1]))
		{
			$_merge_if_not_exists = $args_list[$numargs-1];
		}
		$res=array();
		foreach ($arg_list as $idx => $arg_array)
		{
	
			foreach ($arg_array as $key => $val)
			{
				if($_merge_if_not_exists)
				{
				/*	if( (!is_object($val)) && (!is_array($val)) )
					{*/
						//if(!isset($res[$val]))
						if(!key_exists($key, $res))
						{
							$res[$key]=$val;
						}
				//	}
				}
				else
				{
					$res[$key]=$val;
				}
			}
		}
		return $res;
	}
	
	static function ser_post($ser_name)
	{
		GLOBAL $_BASEDIR;
		$ser_dir = url_seg_add($_BASEDIR,'/test/post_ser');
		$path=url_seg_add($ser_dir,"{$ser_name}.ser");
		file_put_contents($path, serialize($_POST));
	}
	
	static function get_ser($ser_name)
	{
		GLOBAL $_BASEDIR;
		$ser_dir = url_seg_add($_BASEDIR,'/test/post_ser');
		$path=url_seg_add($ser_dir,"{$ser_name}.ser");
		$ser_code = file_get_contents($path);
		return unserialize($ser_code);
	}
	
	static function assoc_array_cut($assoc_arr,$_KEY)
	{
		$res=array();
		foreach ($assoc_arr as $idx => $val)
		{
			if(is_object($val))
			{
				if(property_exists($val,$_KEY))
					$res[$idx]=$val->$_KEY;
			}
			elseif(is_array($val))
			{
				if(isset($val[$_KEY])) $res[$idx]=$val[$_KEY];
			}
		}
		return $res;
	}
	
	static function array_order_num($arr)
	{
		$pos=0;
		$newarray=array();
		foreach($arr as $idx => $val)
		{
			if(is_int($idx))
			{
				$newarray[$pos]=$val;
				$pos++;
			}
			else
				$newarray[$idx]=$val;
		}
		return $newarray;
	}
	
	static function array_insert(&$array, $position, $insert)
	{
		if (is_int($position)) {
			array_splice($array, $position, 0, $insert);
		} else {
			$pos   = array_search($position, array_keys($array));
			$array = array_merge(
					array_slice($array, 0, $pos),
					$insert,
					array_slice($array, $pos)
					);
		}
	}
	
	static function x_array_push($arr,$newitem)
	{
		if(is_array($newitem))
		{
			return merge_arrays($arr, $newitem);
		}
		array_push($arr, $newitem);
		return $arr;
	}
	
	static function _array_diff($arrA,$arrB)
	{
		$newarray=array();
		foreach ($arrA as $El_A)
		{
			if(!in_array($El_A, $arrB))
			{
				$newarray[]=$El_A;
			}
		}
		return $newarray;
	}
	
	// РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р… РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р… РїС—Р…РїС—Р… РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р… $defs РїС—Р…РїС—Р…РїС—Р… РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р… $opt_array
	static function def_options($defs,&$opt_array)
	{
		foreach ($defs as $defkey => $defval)
		{
			if(!isset($opt_array[$defkey]))
				$opt_array[$defkey]=$defval;
		}
	}
	
	static function def_opts($defs,&$opt_array)
	{
		def_options($defs,$opt_array);
	}
	
	static function ximplode($delimeter,$array,$prefix,$suffix,$options=NULL)
	{
		$i=0;
		$str = "";
		foreach($array as $key => $item)
		{
			$itemz = $item;
			$prefixz=strtr($prefix,array('{value}'=>$item,'{key}'=>$key));
			$suffixz=strtr($suffix,array('{value}'=>$item,'{key}'=>$key));
			$delimeterz=strtr($delimeter,array('{value}'=>$item,'{key}'=>$key));
			if($i>0)
			{
				$str = $str.$delimeterz;
			}
			$str=$str.$prefixz.$item.$suffixz;
			$i++;
		}
		return $str;
	}
	/*
	 *    $arr - РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…, РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р… РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…, РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р… РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…
	 * 	  $delimeter - РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…
	 *    $template - РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…. РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р… - РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р… РїС—Р…РїС—Р… РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…, РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р… РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р… - {%val} - РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р… (РїС—Р…РїС—Р…РїС—Р…РїС—Р… РїС—Р…РїС—Р… РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…) {idx} - РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…
	 *    $onelement - РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р… РїС—Р… РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р… &$theval,&$idx,&$thetemplate,&$ctr,$thedelimeter
	 * */
	static function xx_implode($arr,$delimeter,$template,$onelement=NULL)
	{
		$ctr=0;
		$str="";
		foreach ($arr as $idx => $val)
		{
			$thetemplate = $template;
			$thedelimeter = $delimeter;
	
			if(!is_array($val))
			{
				$theval["%val"]=$val;
			}
			else
			{
				$theval=$val;
	
			}
			if($onelement!=NULL)
			{
	
				$onelement($theval,$idx,$thetemplate,$ctr,$thedelimeter);
			}
			$theval["idx"]=$idx;
	
		//	print_r($theval);
	
			$newstr = x_make_str($thetemplate,$theval);
			if($ctr>0)
				$str=$str.$thedelimeter.$newstr;
			else
				$str=$newstr;
			$ctr++;
		}
		return $str;
	}
	
	static function x_array_walk(&$arr,$onelement)
	{
		foreach($arr as $idx => $val)
		{
			$return = false;
			$onelement($idx,$val,$return);
			if($return)
				return;
		}
	}
	
	static function get_by_key_case_no_sensitive($hash,$key)
	{
		foreach ($hash as $_key => $val)
		{
			if(strtolower($key)===strtolower($_key))
				return $val;
		}
		return NULL;
	}
	
	static function x_make_str($str,$ptrn)
	{
		$ptrn2=array("{%0}"=>$ptrn);
		if(is_array($ptrn))
		{
			foreach ($ptrn as $key => $val)
			{
				$ptrn2["{".$key."}"]= (string)$val;
			}
		}
		elseif(is_object($ptrn))
		{
			$vars = get_class_vars($ptrn);
			foreach ($vars as $key => $val)
			{
				$ptrn2["{".$key."}"]=$val;
			}
		}
		else
		{
			$ptrn=array('%val'=>$ptrn);
		}
		$str_res = strtr($str,$ptrn2);
	
		$str_res = exe_php_str($str_res,$ptrn);
		//mul_dbg($res);
		return $str_res;
	}
	
	static function exe_php_str($code_str,$addition_vars=array())
	{
		//mul_dbg(debug_backtrace(),false);
	
		foreach ($addition_vars as $var => $val)
		{
			$$var=$val;
		}
	
		ob_start();
		$code_str = "echo ''; ?>{$code_str}<? echo '';";
		eval($code_str);
		$res = ob_get_clean();
		return $res;
	}
	
	static function delete_from_array_by_value($val, &$arr)
	{
		unset($arr[array_search($val,$arr)]);
	}
	
	static function url_seg_add()
	{
		$numargs = func_num_args();
		$arg_list = func_get_args();
		$resstr="";
		$flg_backslash=true;
		if(is_bool($arg_list[$numargs-1]))
		{
			$flg_backslash=$arg_list[$numargs-1];
		}
		foreach ($arg_list as $idx => $arg)
		{
			if((substr($arg,-1)=="/") || (substr($arg,-1)=="\\"))
			{
				$arg = substr($arg,0,-1);
			}
	
			if((substr($arg,0,1)=="/") || (substr($arg,0,1)=="\\") )
			{
				$arg = substr($arg,1,strlen($arg)-1);
			}
	
			if($flg_backslash)
				$arg = strtr($arg,['\\'=>'/']);
	
			if($idx==0)
			{
				$resstr=$arg;
			}
			else
			{
				$resstr=$resstr."/".$arg;
			}
		}
	
		$resstr = strtr($resstr,array('//'=>'/'));
	
		//mul_dbg($arg_list);
	
		if(substr($arg_list[0],0,1)=='/')
		{
			if(substr($resstr[0],0,1)!='/')
			$resstr="/{$resstr}";
		}
		return $resstr;
	
	}
	
	
	
	// РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р… РїС—Р…РїС—Р…РїС—Р…РїС—Р… РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р… РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…
	static function x_file_put_contents($filename,$data,$flags=0,$context=null)
	{
		$parent_path = dirname($filename);
		if(!file_exists($parent_path))
		{
			x_mkdir($parent_path);
		}
		file_put_contents($filename, $data,$flags,$context);
	}
	
	static function file_put_contents_ifne($filename,$data,$flags=0,$context=null)
	{
		if(!file_exists($filename))
			x_file_put_contents($filename, $data,$flags,$context);
	}
	// РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р… РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р… РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р… РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…
	static function x_mkdir($path)
	{
	//	mul_dbg("creating dir ".$path);
		$parent_path = dirname($path);
		if(file_exists($parent_path))
		{
			if(!file_exists($path))
			{
				mkdir($path);
	
			}
		}
		else
		{
			x_mkdir($parent_path);
			mkdir($path);
		}
	}
	
	static function mul_dbg($var,$print_r=true)
	{
		global $_BASEDIR;
		/*if($_MUL_DBG_WORK)
		{*/
			$file_dbg =self::url_seg_add(__DIR__,'debug.txt');
	
			//echo $file_dbg;
	
			if(is_string($var))
			{
				$newstr=$var;
			}
			else
			{
				ob_start();
				if($print_r)
					print_r($var);
				else
					var_dump($var);
				$newstr = ob_get_clean();
			}
	
			$content="";
			if(file_exists($file_dbg))
			{
				$content = file_get_contents($file_dbg);
			}
			$content=$content."
	
		".date("m-d-Y H:i:s.u").": {$newstr}";
	
			self::x_file_put_contents($file_dbg, $content);
	/*	}*/
	}
	// РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р… РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р… РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р… РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…
	static function dir_dotted($dir)
	{
		if((substr($dir,0,2)=='./') || (substr($dir,0,3)=='../'))
		{
			return $dir;
		}
		return url_seg_add('./', $dir);
	}
	/*
	 * 	$array1 - РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…
	 *  $ev_onelement - РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р… function(&$element) return true or false $element - РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р… РїС—Р… РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р… index РїС—Р… value
	 * */
	static function filter_array($array1,$ev_onelement)
	{
		$res = array();
		foreach ($array1 as $idx => $val)
		{
			$element_res=array('index'=>$idx,'value'=>$val);
			$e_res = $ev_onelement($element_res);
			if($e_res)
			{
				$res[$element_res['index']]=$element_res['value'];
			}
		}
		return $res;
	}
	
	static function set_back_page($_URL)
	{
		if($_SESSION['back_page']['change'])
			$_SESSION['back_page']['url']=$_URL;
	}
	
	static function _redirect($_url)
	{
	
		?>
		<script language="javascript">
			document.location = "<?=$_url?>";
		</script>
		<?php
	}
	
	function convert_slash($url)
	{
		return strtr($url,array('\\'=>'/'));
	}
	
	function get_files_in_folder($dir_path,$opts=array())
	{
		def_options(array('dirs'=>false,'basename'=>false,'without_ext'=>false), $opts);
		$d = dir(convert_slash($dir_path));
		$result=array();
		//	echo "РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…: " . $d->handle . "\n";
		//	echo "РїС—Р…РїС—Р…РїС—Р…РїС—Р…: " . $d->path . "\n";
		while (false !== ($entry = $d->read())) {
			if(($entry!="..")&&($entry!="."))
			{
				$filename = url_seg_add($dir_path, $entry);
				if(count($opts)==0)
				{
				}
				else
				{
					if($opts['dirs'])
					{
						if(!is_dir($filename))
						{
							continue;
						}
					}
				}
				if($opts['basename'])
					$result[]=basename($filename);
				elseif($opts['without_ext'])
				{
					$info = pathinfo($filename);
					$result[]=basename($filename,'.'.$info['extension']);
				}
				else
					$result[]=$filename;
	
			}
		}
		$d->close();
		return $result;
	}
	
	static function is_mask($mask)
	{
		return strpos($mask, "*");
	}
	
	static function match_mask($mask,$str)
	{
		if(strpos($mask, "*"))
		{
			$pattern = strtr($mask,".*",".*");
			$pattern = "/$pattern/Uis";
			return preg_match_all($pattern, $str);
		}
		else
			return ($mask==$str);
	}
	
	static function get_nested_dirs($the_dir)
	{
		$filelist = get_files_in_folder($the_dir);
		$the_dirs=array();
		foreach ($filelist as $the_file)
		{
			if(is_dir($the_file))
			{
				$the_dirs[]=$the_file;
			}
		}
		return $the_dirs;
	}
	
	static function grub_code($_function)
	{
		ob_start();
		$_function();
		$res = ob_get_clean();
	
		return $res;
	}
	
	static function GenRandStr($length=6,$space=false) {
	
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
		if($space) $chars=$chars." ";
	
		$code = "";
	
		$clen = strlen($chars) - 1;
		while (strlen($code) < $length) {
	
			$code .= $chars[mt_rand(0,$clen)];
		}
	
		return $code;
	
	}
	// get equal if exists
	static function eql_ife($arr,$key,$val)
	{
		if(isset($arr[$key]))
		{
			return ($arr[$key]==$val);
		}
		return false;
	}
	
	static function calc_ife($arr,$key,$calc_func)
	{
		if(isset($arr[$key]))
		{
			return $calc_func($arr[$key]);
		}
		return false;
	}
	
	static function string_diff($str1,$str2)
	{
		return strtr($str1,array($str2=>''));
	}
	
	static function filepath2url($path)
	{
		global $_BASEDIR;
		$str = url_seg_add($_BASEDIR,string_diff( strtr($path,array('\\'=>'/')), strtr($_SERVER['DOCUMENT_ROOT'],array('\\'=>'/')) ));
		return as_url($str);
	}
	
	static function array_to_pages($the_array,$pagesize=7)
	{
		$res=array();
	
		return $res;
	}
	
	static function as_uri($str)
	{
		return url_seg_add('/', $str);
	}
	
	function as_url($str)
	{
	
		$script_path = realpath($_SERVER['SCRIPT_NAME']);
		//mul_dbg($_SERVER['SERVER_NAME']);
	
		return url_seg_add('/', dirname($_SERVER['SCRIPT_NAME']), $str);
	}
	
	static function unlink_folder($fldr)
	{
		$nested_files=get_files_in_folder($fldr);
		foreach ($nested_files as $nested)
		{
			if(is_dir($nested))
			{
				unlink_folder(dir_dotted($nested));
			}
			else
			{
				//chown($nested, 666);
				if(is_dir($nested))
					rmdir($nested);
				else
					unlink(dir_dotted($nested));
			}
		}
		chown($fldr, 666);
		//if(file_exists($fldr)) echo ";;;";
		if(is_dir($fldr))
			rmdir($fldr);
		else
			unlink($fldr);
	}
	
	static function add_keypair(&$arr,$key,$val)
	{
		if(empty($arr[$key]))
		{
			$arr[$key]=array();
		}
		$arr[$key][]=$val;
	}
	// РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р… РїС—Р…РїС—Р…РїС—Р…РїС—Р…
	static function find_file($search, $dir_path=".", $rootonly=FALSE)
	{
		if(!file_exists($dir_path))
		{
			return array();
		}
		$d = dir($dir_path);
		$result=array();
	//	echo "РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…: " . $d->handle . "\n";
	//	echo "РїС—Р…РїС—Р…РїС—Р…РїС—Р…: " . $d->path . "\n";
		while (false !== ($entry = $d->read())) {
			if(($entry!="..")&&($entry!="."))
			{
				$filename = url_seg_add($dir_path, $entry);
				if($entry==$search)
				{
					$result[]=$filename;
				}
	
				if($rootonly==FALSE)
				{
					if(is_dir($filename))
					{
	
						$result_nested = find_file($search, $filename);
						$result = array_merge($result,$result_nested);
					}
				}
			}
		}
		$d->close();
		return $result;
	}
	
}
	// РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р…РїС—Р… url
	class url_parser
	{
		var $scheme;
		var $host;
		var $user;
		var $passw;
		var $path;
		var $fragment;
		var $params;
		var $base_url;
		function __construct($URL=NULL)
		{
			if ($URL==NULL)
				$URL = $_SERVER['HTTP_ORIGIN']."/".$_SERVER['REQUEST_URI'];
		//	mul_dbg($_SERVER);
			while(substr($URL,0,1)=='/')   
	    {
	      $URL = substr($URL,1);
	    }
	    
	    //ul_dbg($URL);
	    
			$this->base_url = $URL;
			$url_parses = parse_url($URL);
	//ul_dbg($url_parses);
	
			$this->scheme = $url_parses['scheme'];
			$this->host = $url_parses['host'];
			$this->user = $url_parses['user'];
			$this->passw = $url_parses['passw'];
			$this->path = $url_parses['path'];
			$this->fragment = $url_parses['fragment'];
	   // mul_dbg($this);
			parse_str($url_parses['query'],$this->params);
		}
	
		function url_base()
		{
				return $this->base_url;
		}
	
		function make_url()
		{
			$str_user = "";
			if(!empty($this->user))
				$str_user = "{$this->user}:{$this->pass}@";
				$query_str = http_build_query($this->params);
	
				$str = $this->scheme."://{$str_user}{$this->host}{$this->path}";
				if(!empty($query_str))
					$str="{$str}?{$query_str}";
					return $str;
		}
	
		function make_changed_url($newvars,$delete=array())
		{
			$str_user = "";
			if(!empty($this->user))
				$str_user = "{$this->user}:{$this->pass}@";
	
			$params_2 = $this->params;
			// add vars
			foreach ($newvars as $key => $val)
			{
					$params_2[$key]=$val;
			}
				// delete vars
			foreach ($delete as $del_fld)
			{
					unset($params_2[$del_fld]);
			}
	    
			$query_str = http_build_query($params_2);			
			
				//mul_dbg($str_user);
	    if(empty($this->scheme))
	    {
	      $str = "{$str_user}{$this->host}{$this->path}";
	    }
			else 
	      $str = $this->scheme."://{$str_user}{$this->host}{$this->path}";
			if(!empty($query_str))
				$str="{$str}?{$query_str}";
				
			$str = preg_replace('#(\/{3,})#Uis', '/', $str);	
			return $str;
		}
	}
	
	
	function parse_code_template($tpl_file,$var_array)
	{
		foreach ($var_array as $var => $val)
		{
			$$var=$val;
		}
	
		ob_start();
		if(file_exists($tpl_file))
			include $tpl_file;
	
		$code = ob_get_clean();
			// php tags
		$code = strtr($code,array('<#'=>'<?','#>'=>'?>'));
	
		$var_array2=array();
		foreach ($var_array as $var => $val)
			{
				if(!is_object($val))
				{
					$var_array2['{'.$var.'}']=(string)$val;
				}
			}
		return strtr($code,$var_array2);
	}
	
	function UcaseFirst($str)
	{
		return ucfirst(strtolower($str));
	}
//function
}