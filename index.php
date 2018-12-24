<?php
use the1utils\MString;

$autoload = (require __DIR__ . '/packs/autoload.php');
$_treep = new \treep\TreeP();
/*
  code - непосредственно строка кода
		 nstart - регулярное выражение стартовых токенов
		 nend  - регулярное выражение конечных токенов
		 comments - регулярное выращение блоков комментариев (однострочных и многострочных) строкалибо массив строк
		 shields - массив экранирующих последовательностей [рег. выр. начало, рег. выр. конец]
 * */


$compiled = $_treep->compile([
	'code'=>file_get_contents('./example.js'),
	'nstart'=>'/((while|for|foreach|if|elseif|switch|try|catch|finally)\((.+)\).*$\s*\{)|((while|for|foreach|if|elseif|switch)\((.+)\).*\s*\{)|(else\s*\{)/',		
	'nend'=>'#}#',
	'comments'=>['#/\*.*\*/#Uis','#\/\/.*$#m'],
	'shields'=>['#\\".*\\"#Uis',"#'.*'#Uis",],
	/*'shields'=>['"','"','clear'=>false],*/
]);
if(is_array($compiled))
{
	echo "<h4>".$compiled['error']."</h4>";
}
else 
{
	$compiled->walk(function($item)
	{
		if(!empty($item->_START_TAG_REGEXP_RESULT))
		{
			echo "<+".$item->_START_TAG_REGEXP_RESULT[0][0]."+>";
		}
		else
			echo "\n\r[".$item->_TEXT."
]";
	});

}
