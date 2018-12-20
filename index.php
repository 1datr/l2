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
	//print_r($compiled);
//	the1utils\utils::mul_dbg($compiled);
}
/*
$ms = new MString("uurrr{if(88) 87878 
if(88==88){
====
// 99990
++++
}
/*
* {
* 		
*/
/*
}");
$ms->addLayer('brackets', '#\{if\((.*)\)#Uis');
$ms->addLayer('comments', ['#\/\*.*\*\/#Uis','#\/\/.*$#m']);
$ms->addLayer('shields', "#\'(.*)\'/#Uis");

?>
<table border="1">
<tr>
<?php 
for($i=0;$i<strlen($ms->content);$i++)
{
	?><td><?=$i?></td><?php 
}
?>
</tr>
<tr>
<?php 
for($i=0;$i<strlen($ms->content);$i++)
{
	?><td><?=$ms->content[$i]?></td><?php 
}
?>
</tr>
</table>
<?php 
foreach($ms->getLayer('comments')->points() as $pt)
{
	echo " ".$pt->position;
}*/