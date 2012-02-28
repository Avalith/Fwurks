<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<title>{$__globals.META.administration_title}</title>
	
	<link rel="stylesheet" href="{url for="`$__paths.styles`styles.php"}" type="text/css" media="screen" />
	{foreach $includeCss as $css}
		<link rel="stylesheet" href="{url for="`$__paths.styles``$css`.css"}" type="text/css" />
	{/foreach}
	<link rel="stylesheet" href="{url for="`$__paths.styles`browser_`$__browser`.css"}" type="text/css" media="screen" charset="utf-8" />	
	
	<script type="text/javascript">
		var __globals = {json_encode($__globals|default:'')};
		var __labels = {json_encode($__labels|default:'')};
		var __basePath = '{url for='//'}';
		var __rootPath = '{url for='/'}';
	</script>
	{foreach $includeJavascripts as $js}
		<script src="{url for="`$__paths.javascripts``$js`.js"}" type="text/javascript"></script>
	{/foreach}
</head>
<body id="{$__controller}">
	<div id="page_container">
		<div id="header">
			
			<div class="info">
				<div class="left">
					&nbsp; <a href="{url for='//administration/profile'}" title="User loged: {$__session->logged_user->_display_name}">{$__session->logged_user->_display_name}</a> &nbsp;&nbsp;
				
					{include file="breadcrumbs.tpl"}
				</div>
				<div class="right">
					{include file="language_select.tpl"}

					<!-- <a href="#" title="Hide sidebar" id="sidebar_toggle">Hide navigation</a>
					&nbsp;|&nbsp; -->
					<a href="{url for="//logout"}">Logout</a>
					&nbsp;|&nbsp;
					<a href="/">Visit site</a>
				</div>
			</div>
			
			<h2><a href="#" title="Antipodes">Antipodes</a></h2>
			
			<h1 class="header">
				{$title|default:$__globals.LISTING_TITLES[$__controller]|default:$__globals.META.administration_title}
				{if $parent} {$parent} {/if}
			</h1>
		</div>
		
		<div id="page_content">
			<div id="cnt" class="column">
				<div class="cnt_left"><div class="cnt_bottom"><div class="cnt_bottom_left"><div class="cnt_top_left">
				<div id="cnt_inn">
					
					<div id="actions">
						{block name='actions'}{/block}
					</div>
					
					{block name='content'}{/block}
				</div>
				</div></div></div></div>
			</div>
			<div id="sidebar" class="column"><div class="inner">
				{admin_menu menu=$_admin_menu value=$_selected_path id="navigation"}
			</div></div>
		</div>
	</div>
</body>
</html>