function ImageBox(rel)
{
	if(!rel){ rel = 'image-box'; }

	var padding = 10;
	var loadingPath;
	var margin = 200;
	
	var shown = false;
	
	$('body').append('<div id="ib-overlay"></div><img id="ib-img" src="data:image;base64," alt="" title="" /><div id="ib-loading"><img src="'+(loadingPath || 'data:image;base64,')+'" /></div>');
	
	$overlay 	= $('#ib-overlay');
	$loading 	= $('#ib-loading');
	$img 		= $('#ib-img');
	
	function hideImg()
	{
		shown = false;
		$overlay.fadeOut();
		$loading.hide();
		$img.fadeOut();
	};
	hideImg();
	
	$(window).keydown(function(e){ if(shown == true && e.keyCode == 27){ hideImg(); } });
	
	$overlay.click( function(){ hideImg(); } );
	$img.click( function(){ hideImg(); } );
	
	$('a[rel*='+rel+']').click(function()
	{
		shown = true;
		var elem = this;
		var pageSize = refreshPageSize()
	
		$loading.css('margin-top', $(window).scrollTop()).fadeIn();
		$overlay.css('margin-top', $(window).scrollTop()).fadeIn();
		$('html, body').addClass('noscroll');
		
		
		preloader = new Image();
		preloader.onload = function()
		{
			$img.attr('src', elem.href);
			
			var x = pageSize.width - margin;
			var y = pageSize.height - margin ;
			var w = preloader.width;
			var h = preloader.height;
			
			if(w > x)
			{
				h = h * (x / w);
				w = x;
				if(h > y){ w = w * (y / h);	h = y; }
			}
			else if (h > y)
			{
				w = w * (y / h);
				h = y;
				if(w > x){ h = h * (x / w);	w = x; }
			}
			
			var marginLeft = ( (w + 2 * padding) / 2 ) * -1;
			var marginTop = ( (h + 2 * padding) / 2 ) * -1;
			
			$img.width(w).height(h)
				.css({ padding: padding+"px", marginLeft: marginLeft+"px", marginTop: $(window).scrollTop()+marginTop+"px" })
				.fadeIn();
			$loading.fadeOut();
		}
		
		preloader.src = elem.href;
		
		return false;
	});
	
	function refreshPageSize(){ return { width: $(window).width(), height: $(window).height() }; }
}

$(function(){ ImageBox(); })
