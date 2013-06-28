/* Copyright (c) 2008 Kean Loong Tan http://www.gimiti.com/kltan
 * http://www.gimiti.com/kltan/wordpress/?p=29
 * 
 * Edited Karamfil: 
 *	+ better performance 
 * 	+ styles are now outside
 * 	+ added handle
 * 	+ added 3 events in the options
 * 		- onstart
 * 		- ondrag
 * 		- onend
 *
 * Licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * Copyright notice and license must remain intact for legal use
 * jTree 1.0
 * Version: 1.0 (May 5, 2008)
 * Requires: jQuery 1.2+
 */
 
(function($){
// Plugin Code Begin

$.fn.jTree = function(options)
{
	var defaults = {
		showHelper: true, childOff: 10, snapBack: 200,
		level : 0,
		handle: '', handle_parent: '',
		// events
		onstart: function(){}, ondrag: function(){}, onend: function(){}
	};
	
	var $jTreeHelper = $('<ul id="jTreeHelper"></ul>').appendTo('body');
	var opts = $.extend({}, defaults, options);
	var $cur = 0, curOff = 0, curOldParent = 0, off = 0, w = 0, hover = 0;
	var str='<li class="jTreePlacement"></li>';
	var $container = $(this);
	
	//events are written here
	$(this).find('li'+opts.handle).mousedown(function(e)
	{
		if ($jTreeHelper.is(':not(:animated)') && e.button != 2) 
		{
			$('body').css('cursor','move').append(str); // append jTreePlacement to body and hides
			$('.jTreePlacement').hide();
			
			// get initial state, cur and offset
			$cur = opts.handle ? $(this).parents(opts.handle_parent) : $(this);
			
			//get the current li and append to helper
			$jTreeHelper.append($cur.clone());
			
			curOff = $cur.offset();
			$cur.hide();
			
			curOldParent = $cur.parents('li:eq(0)');
			
			// show initial helper
			$jTreeHelper.css({ position: 'absolute', top: e.pageY + 5, left: e.pageX + 5 }).addClass('jTreeDraglet').hide();
			
			if(opts.showHelper){ $jTreeHelper.show(); }
			
			// start binding events to use
			$(document).bind('selectstart', doNothing)		// prevent text selection
			
			$container.bind('mousemove', sibOrChild) 		// in single li put placement in correct places, also move the helper around
				.find('li')
					.bind('dblclick', doNothing)			// double click is destructive, better disable
					.bind('mouseover', getInitial) 			// in single li calculate the offset, width height of hovered block
					.bind('mousemove', putPlacement);		// in container put placement in correct places, also move the helper around
			
			$(document).bind('mousemove', helperPosition);	// handle mouse movement outside our container
		}
		
		opts.onstart($cur, curOldParent);
		
		//prevent bubbling of mousedown
		return false;
	});
	
	// in single li or in container, snap into placement if present then destroy placement and helper 
	// then show snapped in object/li also destroys events
	$(this).find('li'+opts.handle).andSelf().mouseup(function(e)
	{
		if(!$cur){ return; }
		
		$('body').css('cursor','default');
		
		if(opts.level > 0)
		{
			len = $('.jTreePlacement').parents('li').length;
			len++ // include self;
			
			elem = $cur;
			while((elem = elem.children('ul').children('li')).length){ len++; }
		
			if(len <= opts.level)
			{
				// if placementBox is detected
				if($('.jTreePlacement').is(':visible')){ $cur.insertBefore('.jTreePlacement').show(); }
				opts.onend($cur, curOldParent);
			}
			else
			{
				alert(__labels.__globals.ERRORS.tree_deep);
			}
		}
		else
		{
			if($('.jTreePlacement').is(':visible')){ $cur.insertBefore('.jTreePlacement').show(); }
			opts.onend($cur, curOldParent);
		}
		
		$cur.show();
		
		// remove helper and placement box and clean all empty ul
		$container.find('ul:empty').remove();
		
		$jTreeHelper.hide().empty();
		$('.jTreePlacement').remove();	
		
		// remove bindings
		destroyBindings();
		
		return false;
	});
	
	$(document).mouseup(function(e)
	{
		$('body').css('cursor','default');
		if (!$jTreeHelper.is(':empty'))
		{
			$jTreeHelper.animate({ top: curOff.top, left: curOff.left }, opts.snapBack, function()
			{
				$jTreeHelper.empty().hide();
				$('.jTreePlacement').remove();
				$cur.show();
				
				opts.onend($cur, curOldParent);
			});
			destroyBindings();
		}
		
		return false;
	});
	
	//functions are written here
	var doNothing = function(){	return false; };
	
	var destroyBindings = function()
	{
		$(document).unbind('selectstart', doNothing)
		
		$container.unbind('mousemove', sibOrChild)
			.find('li')
				.unbind('dblclick', doNothing)
				.unbind('mouseover', getInitial)
				.unbind('mousemove', putPlacement);
		
		$(document).unbind('mousemove', helperPosition);
			
		return false;
	};
	
	var helperPosition = function(e)
	{
		$jTreeHelper.css({ top: e.pageY + 5, left: e.pageX + 5 });
		$('.jTreePlacement').remove();
		
		return false;
	};
	
	var getInitial = function(e)
	{
		off 	= $(this).offset();
		h 		= $(this).height();
		w 		= $(this).width();
		hover 	= this;
		
		return false;
	};
	
	var sibOrChild = function(e)
	{
		$jTreeHelper.css({ top: e.pageY + 5, left: e.pageX + 5 });
		return false;
	};
	
	var putPlacement = function(e)
	{
		$cur.hide();
		$jTreeHelper.css({ top: e.pageY + 5, left: e.pageX + 5 });
		
		// inserting before
		if ( e.pageY >= off.top && e.pageY < (off.top + h/2 - 1) )
		{
			if (!$(this).prev().hasClass('jTreePlacement'))
			{
				$('.jTreePlacement').remove();
				$(this).before(str);
			}
		}
		// inserting after
		else if (e.pageY >(off.top + h/2) &&  e.pageY <= (off.top + h) )
		{
			// as a sibling
			if (e.pageX > off.left && e.pageX < off.left + opts.childOff)
			{
				if (!$(this).next().hasClass('jTreePlacement'))
				{
					$('.jTreePlacement').remove();
					$(this).after(str);
				}
			}
			// as a child
			else if (e.pageX > off.left + opts.childOff)
			{
				$('.jTreePlacement').remove();
				$(this).find('ul').length == 0 ? $(this).append('<ul>'+str+'</ul>') : $(this).find('ul').prepend(str);
			}
		}
		if($('.jTreePlacement').length > 1){ $('.jTreePlacement:first-child').remove(); }
		
		opts.ondrag($cur, curOldParent);
		
		return false;
	}
	
	var lockIn = function(e)
	{
		// if placement box is present, insert before placement box
		if($('.jTreePlacement').length == 1){ $cur.insertBefore('.jTreePlacement'); }
		$cur.show();
		
		// remove helper and placement box
		$jTreeHelper.empty().hide();
		$('.jTreePlacement').remove();
		
		return false;
	}
};

// Plugin Code End	  
})(jQuery);
