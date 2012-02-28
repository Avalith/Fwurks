$(function()
{
	$('h1').after('<h2 id="toc-toc">Table of Contents</h2><ul id="toc"></ul>');
	var $toc = $('#toc');
	
	$('h2, h3, h4, h5, h6').slice(1).each(function()
	{
		var $this = $(this);
		var id = $this.attr('id') || $this.text().toLowerCase().replace(/\W+/g, '_');
		$this.attr('id', 'toc-'+id);
		
		var level = this.tagName.substr(1);
		var $tocItem = $toc;
		
		if(level > 2)
		{
			var _this = $this;
			var $parent_tocItem = $( '#item'+$this.prevAll('h'+(level-1)+':eq(0)').attr('id') );
			
			while(!$parent_tocItem.length)
			{
				_this = _this.parent();
				$parent_tocItem = $( '#item'+_this.prevAll('h'+(level-1)+':eq(0)').attr('id') );
			}
			
			$tocItem = $parent_tocItem.children('ul');
			if(!$tocItem.length){ $tocItem = $('<ul></ul>').appendTo($parent_tocItem); }
		}
		
		var num = $tocItem.children().length+1;
		if($parent_tocItem)
		{ 
			num = $parent_tocItem.children('a').children('.number').text()+num; 
			if(!$parent_tocItem.find('.button').length)
			{
				$('<span class="button collapse"></span>').appendTo($parent_tocItem).click(function()
				{
					$(this).toggleClass('expand').toggleClass('collapse').siblings('ul').slideToggle();
				});
			} 
		}
		
		
		$tocItem.append('<li id="itemtoc-'+id+'"><a href="#toc-'+id+'"><strong class="number">'+num+'.</strong> '+$this.text()+'</a></li>');
		$this/*.append('<a class="up-to-toc" href="#toc-toc">^ TOC</a>')*/.prepend('<strong class="number">'+num+'.</strong> ');
		
		$this.mouseenter(function(){ $toc.find('.selected').removeClass('selected'); $('#itemtoc-'+id+' > a').addClass('selected'); });
	});
	
	$toc.find('a').click(function(){ $toc.find('.selected').removeClass('selected'); $(this).addClass('selected'); })
});
