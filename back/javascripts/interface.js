$(function()
{
	language_now = $('#id_laguages_select').change(function()
	{
		location.pathname = location.pathname.replace('/'+language_now+'/', '/'+$(this).val()+'/');
	}).val();
	
	$('#actions .button.delete').click(function()
	{
		ids = $('.listing tbody :checkbox').serialize().replace(/record=/g, '').replace(/&/g, ',');
		
		if(ids){ this.href += '/?ids='+ids }
		else { return false; }
		
		return confirm(__labels.__globals.ERRORS.listing_delete) ? true : false;
	});
	
	$('.listing .icon.delete').click(function(){ return confirm(__labels.__globals.ERRORS.listing_delete) ? true : false; });
	$('.tree .icon.delete').click(function(){ return confirm(__labels.__globals.ERRORS.tree_delete) ? true : false; });
	
	$('.listing_filter form').submit(function (){

		$('.listing_filter .apply.button').click();
		return false;
	});
	$('.listing_filter .apply.button').click(function()
	{
		fields = $('input, select', '#listing-filter, .listing_filter').serialize();
		
		fields_names = [];
		t_fields = fields.split('&');
		for(f in t_fields){ fields_names[f] = t_fields[f].split('=')[0]; }
		fields_names = fields_names.join('|');
		
		eval( 'reg = /(?:[A-Za-z0-9_-]+=|page=[0-9]+|(?:'+fields_names+')=[A-Za-z0-9_%-]+)(?:&|$)/g' )
		url = location.search.substr(1).replace(reg, '').replace(/&*$/, '');
		
		fields = fields.replace(/[A-Za-z0-9_-]+=\+*(&|$)/g, '').replace(/&*$/, '');
		
		url = url + (url && fields ? '&' : '') + fields;
		
		if(this.href.indexOf('?') > 0){ this.href = this.href.substr(0, this.href.indexOf('?')); }
		this.href = this.href.replace('#') || '';
		link = this.href ? this.href + '?' : '';
		
		if(url || location.search){ location.href = link + url; }
		
		return false;
	});
	
	
	// Tabs BEGIN
	if($('.tabs_ul li').length)
	{
		$('.tab_container, .tab_container h3').hide();
		$( '#'+$('.tabs_ul li.selected a').attr('href').substr(1)+'.tab_container' ).show();
		
		$('.tabs_ul a').click(function()
		{
			$('.tabs_ul li').removeClass('selected')
			$(this).parents('li').eq(0).addClass('selected');
			
			$('.tab_container:visible').hide();
			$('#' + $(this).attr('href').substr(1) + '.tab_container').show();
			return false;
		});
		
		$('.errors label').click(function()
		{
			lang = $(this).text().match(/\(([a-z\s]+)\)/i)[1];
			$('.tabs_ul li:not(.selected) a:contains('+lang+')').click();
		});
	}
	
	$("table.listing thead :checkbox[name=records]").change(function()
	{
		var checkboxes = $(this).parents('table:eq(0)').find('tbody :checkbox[name=record]');
		$(this).is(':checked') 
			? checkboxes.attr("checked","checked").parent().parent().addClass('checked-row') 
			: checkboxes.removeAttr("checked").parent().parent().removeClass('checked-row');
	})
		.parents('table:eq(0)').find('tbody :checkbox[name=record]').change(function()
		{
			$(this).parents('tr:eq(0)').toggleClass('checked-row');
			$(this).is(':checked') || $(this).parents('table:eq(0)').find('thead :checkbox[name=records]').removeAttr("checked");
		});
	
	// Forms
	$('form').each(function(){ new Validator($(this)); });
	$('form #id_cancel').click(function(){ history.back(-1); return false; });

	$('#sidebar_toggle').click(function(){
		if ( !$('body').hasClass('no_sidebar') )
		{
			$('#sidebar').animate({ opacity: 0 }, 100, function ()
			{
				$('#page_content').animate({ paddingLeft: '20px' }, 300 );
				$('body').toggleClass('no_sidebar');
				$('#sidebar_toggle').text('Show navigation');
			});
		}
		else
		{
			$('#page_content').animate({ paddingLeft: '230px' }, 300, function()
			{
				$('#sidebar').animate({ opacity: 100 }, 100);
				$('body').toggleClass('no_sidebar');
				$('#sidebar_toggle').text('Hide navigation');
			});
		}
	});

	$(':checkbox.pages[name=navigation]').bind('load change', function(){ $(this).is(':checked') ? $('#id_parent_id').removeAttr('disabled') : $('#id_parent_id').attr('disabled', 'disabled'); }).trigger('load');
	
	// Listing Movers
	$('.movers')
		.children('.icon.arrow_up').click(function()
		{
			var $tr = $(this).parents('tr:eq(0)');
			var $tr_prev = $tr.prev();
			var $order_index = $tr.find('.order_index');
			var order_index = $order_index.val();
			
			if($tr_prev.length)
			{
				$newOrder = $tr_prev.toggleClass('odd').find('.order_index');
				$tr.toggleClass('odd').find('.order_index').val($newOrder.val()).end().insertBefore($tr_prev);
				$newOrder.val(order_index);
			}
			return false;
		})
		.end()
		.children('.icon.arrow_down').click(function()
		{
			var $tr = $(this).parents('tr:eq(0)');
			var $tr_next = $tr.next();
			var $order_index = $tr.find('.order_index');
			var order_index = $order_index.val();
			
			if($tr_next.length)
			{
				$newOrder = $tr_next.toggleClass('odd').find('.order_index');
				$tr.toggleClass('odd').find('.order_index').val($newOrder.val()).end().insertAfter($tr_next);
				$newOrder.val(order_index);
			}
			return false;
		})
		.end()
		.parent('tr')
			.mousedown(function(event)
			{
				var $tr = $(this).addClass('moving');
				var $table = $tr.parents('table:eq(0)');
				var $order_index = $tr.find('.order_index');
				var order_index = $order_index.val();
				
				$('body')
					.bind('mousemove.listing_order', function(event)
					{
						document.onselectstart = false;
					
						event.stopPropagation();
						event.bubbles = false;
						$tr_prev = $tr.prev();
						$tr_next = $tr.next();
						
						if($tr_prev.length && event.pageY < $tr_prev.offset().top + $tr_prev.height())
						{
							$tr.toggleClass('odd').insertBefore($tr_prev.toggleClass('odd'));
							var $prev_index = $tr_prev.find('.order_index');
							new_index = $order_index.val($prev_index.val()).val();
							$prev_index.val(order_index);
							order_index = new_index;
						}
						if($tr_next.length && event.pageY > $tr_next.offset().top)
						{
							$tr.toggleClass('odd').insertAfter($tr_next.toggleClass('odd'));
							var $next_index = $tr_next.find('.order_index');
							new_index = $order_index.val($next_index.val()).val();
							$next_index.val(order_index);
							order_index = new_index;
						}
						
					})
					.bind('mouseup.listing_order', function()
					{
						setTimeout(function(){ $tr.removeClass('moving'); }, 1000);
						$(this).unbind('.listing_order');
					});
				// END $('body')
				
				return false;
			})
			.find('.icon.arrow_updown').click(function(){ return false; });
		// END $('.movers').children('.icon.arrow_updown')
	// END $('.movers')
	
	$('.save_listing_order').click(function()
	{
		$.ajax(
		{
			type: "POST",
			url: '?save_listing_order=1',
			data: $('input[name^="listing_order"]').serialize(),
			complete: function(){ /*humanMsg.displayMsg(__labels.__globals.ERRORS.listing_saved);*/ }
		});
		
		return false;
	});
	// END Listing Movers
	
	// User display name
	var display_name_custom = __globals['DISPLAY_NAMES']['custom'];
	$('#id_display_name').change(function()
	{
		if($(this).val() == display_name_custom){ $('#nick_name').show(); }
		else { $('#nick_name').hide(); }
	});
	if($('#id_display_name').val() == display_name_custom){ $('#nick_name').show(); }
	// END User display name
		
	// ############# CUSTOMS
	
	if ( $('.main_form').height() > $('.sidebar_form').height() )
	{
		$('.sidebar_form').height($('.main_form').height());
	}
	
});


