<?php
$autoload = (require __DIR__ . '/packs/autoload.php');
$_treep = new \treep\TreeP();
/*
  code - ��������������� ������ ����
		 nstart - ���������� ��������� ��������� �������
		 nend  - ���������� ��������� �������� �������
		 comments - ���������� ��������� ������ ������������ (������������ � �������������) ���������� ������ �����
		 shields - ������ ������������ ������������������� [���. ���. ������, ���. ���. �����]
 * */

$compiled = $_treep->compile([
	'code'=>file_get_contents('./example.js'),
	'nstart'=>'/((while|for|foreach|if|elseif|switch|try|catch|finally)\((.+)\).*$\s*\{)|((while|for|foreach|if|elseif|switch)\((.+)\).*\s*\{)|(else\s*\{)/',		
	'nend'=>'#}#',
	'comments'=>['#/\*.*\*/#Uis','#\/\/.*$#m'],
	'shields'=>[
				['"','"','clear'=>false],],
]);


var_dump($compiled);