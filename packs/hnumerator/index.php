<?php
namespace hnumerator{

	/* Иерархический нумератор */
	class HNnumerator
	{
		VAR $buf=[];
		VAR $idx=0;
		VAR $delimeter='.';

		function __construct($str='',$del='.')
		{
			$this->delimeter = $del;
			if(!empty($str))
			{
				$this->from_str($str);
			}
			else
			{
				$this->buf[]=1;

			}
		}

		private function from_str($str)
		{
			$this->buf = explode($this->delimeter, $str);
			$this->idx = count($this->buf)-1;
		}

		function inc()
		{
			$this->buf[$this->idx]++;
		}

		function up()
		{
			if($this->idx>=0)
			{
				unset($this->buf[$this->idx]);
				$this->idx--;
			}
		}

		function down()
		{
			$this->buf[]=1;
			$this->idx++;
		}

		function getText()
		{
			return implode($this->delimeter,$this->buf);
		}



		public function getString(){
			return $this->getText();
		}
	}

}