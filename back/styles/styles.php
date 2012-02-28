<?php

require_once __DIR__.'/../../system/configs/system.config.php';

$styles = array( 'common', 'fonts', 'forms', 'icons', 'image_box', 'tabs', 'lists', 'navigation', 'tree' );

$compiled_file = '__compiled.css';
if(!SystemConfig::DEVELOPMENT && ($compiledCss = file_get_contents($compiled_file))){ echo $compiledCss; exit; }

$output = '';
$replacer = array();
foreach($styles as $css)
{
	$output .= "\n\n/*\n* -------------------------------\n* $css \n* ------------------------------- \n*/\n\n";
	
	$css = file_get_contents($css.'.css');
	
	preg_match('/{{(.*)}}/us', $css, $string);
	$css = preg_replace('/{{.*}}/us', '', $css);
	
	if(isset($string[1]))
	{
		preg_match_all('/(\$[a-z\d]+)\s*:\s*(.+)/ui', $string[1], $_vars, PREG_SET_ORDER);
		foreach($_vars as $v){ $vars[$v[1]] = $v[2]; }
		$css = strtr($css, $vars);
		
		$reg_params = '\s*\(\s*([^\)]*)\s*\)';
		preg_match_all("/(@[a-z\d]+){$reg_params}\s*{\s*([^}]+)\s}/ui", $string[1], $_blocks, PREG_SET_ORDER);
		foreach($_blocks as $b)
		{
			$params = array();
			preg_match_all('/([a-z\d\$]+)\s*(?:=\s*([^,]*))?/ui', $b[2], $_params, PREG_SET_ORDER);
			foreach($_params as $p){ $params[] = (object)array('key' => $p[1], 'value' => isset($p[2]) ? $p[2]: ''); }
			$b = (object)array
			(
				'block'		=> $b[1],
				'params' 	=> $params,
				'body'		=> $b[3],
			);
			
			$css = preg_replace_callback("/{$b->block}{$reg_params}/ui", function($m) use ($b)
			{
				$params = explode(',', $m[1]);
				
				$replacer = array();
				foreach($b->params as $i => $p)
				{
					isset($params[$i]) && $param = trim($params[$i]);
					$replacer[$p->key] = $param ?: $p->value;  
				}
				
				return strtr($b->body, $replacer);
				
			}, $css);
		}
		
		$css = preg_replace_callback('/(?>([#.\w\s,: ()+-]+){|})/ui', function($m)
		{
			static $parents = array(), $level = -1, $last_level;
			
			if($m[0] == '}')
			{
				$outer = $last_level - $level;
				unset($parents[$level--]);
				if($outer == 1){ $last_level = $level + 1; return ''; }
				return '}';
			}
			else
			{
				$inner = $level - $last_level + 1;
				$parents[++$level] = trim($m[1]);
				
				$selector = ''; if($inner){ $selector = "}\n"; $last_level = $level; }
				$selector .= implode(' ', $parents);
				
				return $selector ? "\n{$selector}{" : '{';
			}
		}, $css);
	}
	
	$output .= $css;
}

header('Content-Type: text/css');
echo $output;
file_put_contents($compiled_file, $output);

?>
