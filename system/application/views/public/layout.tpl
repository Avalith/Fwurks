<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<title>Title</title>
	
	<link rel="stylesheet" href="{url for="`$__paths.styles`styles.php"}" type="text/css" media="screen" />
	{foreach $includeCss as $css}
		<link rel="stylesheet" href="{url for="`$__paths.styles``$css`.css"}" type="text/css" media="screen" />
	{/foreach}
	<link rel="stylesheet" href="{url for="`$__paths.styles`browser_`$__browser`.css"}" type="text/css" />
	
	<script src="{url for="`$__paths.javascripts`jquery.js"}" type="text/javascript"></script>
	<script src="{url for="`$__paths.javascripts`interface.js"}" type="text/javascript"></script>
	{foreach $includeJavascripts as $js}
		<script src="{url for="`$__paths.javascripts``$js`.js"}" type="text/javascript"></script>
	{/foreach}
	
	<script type="text/javascript">
		var __globals = {json_encode($__globals|default:'')};
		var __labels = {json_encode($__labels|default:'')};
		var __basePath = '{url for='//'}';
		var __rootPath = '{url for='/'}';
	</script>
</head>
<body>
	<div id="page_container">
		<div id="header">
			<h1><a href="{url for='//'}">Title</a></h1>
		</div>
		
		<div id="page_content">
			<div id="cnt">
				{block name='content'}{/block}
				<div class="cleaner">&nbsp;</div>
			</div>
		</div>
		<div class="cleaner">&nbsp;</div>
		
		<div id="footer">
			
		</div>
	</div>
	
	{if $__google_analytics}
		{literal}
			<script type="text/javascript">
				var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
				document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
			</script>
			<script type="text/javascript">
				try {
					var pageTracker = _gat._getTracker('{/literal}{$__google_analytics}{literal}');
					pageTracker._trackPageview();
				} catch(err) {}
			</script>
		{/literal}
	{/if}
	
</body>
</html>
