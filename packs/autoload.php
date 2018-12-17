<?php
/**
 * This file is part of FPDI
*
* @package   setasign\Fpdi
* @copyright Copyright (c) 2018 Setasign - Jan Slabon (https://www.setasign.com)
* @license   http://opensource.org/licenses/mit-license The MIT License
*/

spl_autoload_register(function($class) 
{
	$explodes=explode('\\',$class);
	$pack_path = __DIR__.'/'.implode('/',$explodes);
	$fullpath = $pack_path."/index.php";
	$utils_path =  $pack_path."/utils.php";
	//echo $fullpath;
	if (file_exists($fullpath)) 
	{
	
		require_once $fullpath;
		//require_once $utils_path;
	}
	else 
	{
		$explodes2 = $explodes;
		unset($explodes2[count($explodes2)-1]);
		$pack_path = __DIR__.'/'.implode('/',$explodes2);
		$fullpath = $pack_path."/index.php";
		$utils_path =  $pack_path."/utils.php";
		if(file_exists($fullpath))
		{
			require_once $fullpath;
		//	require_once $utils_path;
		}
		else 
		{
			$pack_path = __DIR__.'/'.$explodes[count($explodes)-1];
			$fullpath = $pack_path."/index.php";
		//	$utils_path =  $pack_path."/utils.php";
			if(file_exists($fullpath))
			{
				require_once $fullpath;
				//	require_once $utils_path;
			}
		}
	}
	/*
	if (strpos($class, 'setasign\treep\\') === 0) {
		$filename = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, 14)) . '.php';
		$fullpath = __DIR__ . DIRECTORY_SEPARATOR . $filename;

		if (file_exists($fullpath)) {
		
			require_once $fullpath;
		}
	}*/
});
