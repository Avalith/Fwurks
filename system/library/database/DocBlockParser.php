<?php


//	$str = '
//		/**
//		 * @author asd: "Karamfil\' Ivanov" zxc
//		 * @man123y-to-many targetEntity: Group 123:asd !validate
//		 * @JoinTable name: users_groups
//		 * @Id
//		 * @column
//		 * @GeneratedValues strategy: SEQUENCE true-attribute
//		 * @SequenceGenerator sequenceName: tablename_seq initialValue: 1 allocationSize: 100
//		**/';

function parse_doc_block($str)
{
	$params = array();
	
	$q = preg_match_all("~(@[^ \t\n:]+)[ \t]*(.*)~", $str, $matches, PREG_SET_ORDER);
	foreach($matches as &$m)
	{
		$k = strtolower($m[1]); $v = $m[2];
		if($v && preg_match_all("~([^ \t\n:]+)(?:\:[ \t]*(?:\"(.*)\"|([^ \t\n:]+)))?~", $v, $v, PREG_SET_ORDER))
		{
			foreach($v as $i)
			{
				$_k = $i[1]; $_v = $i[2];
				
				if(!$_v)
				{
					if($_k{0} == '!')	{ $_k = substr($_k, 1); $_v = false; } 
					else				{ $_v = true; }
				}
				
				$params[$k][$_k] = $_v;
			}
		}
		else
		{
			$params[$k] = true;
		}
	}
	
	return $params;
}


?>
