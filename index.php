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

/*
$compiled = $_treep->compile([
	'code'=>file_get_contents('./example.js'),
	'nstart'=>'/((while|for|foreach|if|elseif|switch|try|catch|finally)\((.+)\).*$\s*\{)|((while|for|foreach|if|elseif|switch)\((.+)\).*\s*\{)|(else\s*\{)/',		
	'nend'=>'#}#',
	'comments'=>['#/\*.*\*/#Uis','#\/\/.*$#m'],
/*	'shields'=>[
				['"','"','clear'=>false],],
]);
*/

$ms = new MString("aaa{if(h==7) sss/*{if(x==0) */ss{if(y==9) 6666 }}}");
$ms->addLayer('brackets', '#\{if\((.*)\)#Uis');

echo $ms->content."<br />";
foreach($ms->getLayer('brackets')->points() as $p)
{
	echo " ".$p->position;
}

$ms2=$ms->substr($ms->getLayer('brackets')->points()[1], -1);
//print_r($ms->getLayer('brackets')->points()[0]);
echo "<br />".$ms2->content."<br />";
foreach($ms2->getLayer('brackets')->points() as $p)
{
	echo " ".$p->position;
}
//print_r($ms);
//var_dump($compiled);