<?php
use phpjs\phpjs as phpjs;

$autoload = (require __DIR__ . '/packs/autoload.php');

/*
  code - ��������������� ������ ����
		 nstart - ���������� ��������� ��������� �������
		 nend  - ���������� ��������� �������� �������
		 comments - ���������� ��������� ������ ������������ (������������ � �������������) ���������� ������ �����
		 shields - ������ ������������ ������������������� [���. ���. ������, ���. ���. �����]
 * */


$compiled = phpjs::compile(file_get_contents('./example.js'));
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
			echo "[".$item->number."]<+".$item->_START_TAG_REGEXP_RESULT[0][0]."+>";
		}
		else
			echo "\n\r[".$item->number."][".$item->_TEXT."]";
	});

}
