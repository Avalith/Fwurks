$(function()
{
	// Jump to Page
	$('.page-jump input').keypress(function(e){ if(e.keyCode == 13){ $(this).next().click(); return false; } });
	$('.page-jump a').click(function()
	{
		var val = $('.page-jump input').val();
		var url = location.href.replace(/(\?|&)page=\d+/, '$1page='+val); 
		location.href = url.indexOf('page=') > 0 ? url : (url + (url.indexOf('?') > 0 ? '&' : '?') + 'page='+val);
		return false; 
	});
	
	language_now = $('#id_laguages_select').change(function()
	{
		location.pathname = location.pathname.replace('/'+language_now+'/', '/'+$(this).val()+'/');
	}).val();
	
	$('#actions .button.delete').click(function()
	{
		ids = $('.listing tbody :checkbox').serialize().replace(/record=/g, '').replace(/&/g, ',');
		
		var path = this._href ? this._href : (this._href = $(this).attr('href'));
		if(ids){ this.href = path+'/?ids='+ids }
		else { return false; }
		
		return confirm(__globals.ERRORS.listing_delete) ? true : false;
	});
	
	$('.listing .icon.delete').click(function(){ return confirm(__globals.ERRORS.listing_delete) ? true : false; });
	$('.tree .icon.delete').click(function(){ return confirm(__globals.ERRORS.tree_delete) ? true : false; });
	
	$('.listing_filter form').submit(function (){

		$('.listing_filter .apply.button').click();
		return false;
	});
	$('.listing_filter .apply.button').click(function()
	{
		var fields = $('input, select', '#listing-filter, .listing_filter');
		
		var fields_names = [];
		$('input, select', '#listing-filter, .listing_filter').each(function(i){ fields_names[i] = this.name.replace('[]', '%5B%5D'); });
		fields_names = fields_names.join('|');
		
		var reg = new RegExp('(?:[A-Za-z0-9_+-]+=|page=[0-9]+|(?:'+fields_names+')=[A-Za-z0-9_%+\(\)-]+)(?:&|$)', 'g');
		var url = location.search.substr(1).replace(reg, '').replace(/&*$/, '');
		
		fields = fields.serialize().replace(/[A-Za-z0-9_\(\)-]+=\+*(&|$)/g, '').replace(/&*$/, '');
		url = url + (url && fields ? '&' : '') + fields;
		
		if(this.href.indexOf('?') > 0){ this.href = this.href.substr(0, this.href.indexOf('?')); }
		this.href = this.href.replace('#') || '';
		link = this.href ? this.href + '?' : '';
		
		if(url || location.search.substr(1)){ location.href = link + url; }
		
		return false;
	});
	
	
	// Tabs
	if($('.tabs_ul li').length)
	{
		$('.tab_container, .tab_container h3').hide();
		var $selected = $('.tabs_ul li.selected');
		$( '#'+ ($selected.length ? $selected : $('.tabs_ul li:first-child').addClass('selected') ).children().attr('href').substr(1)+'.tab_container' ).show();
		
		$('.tabs_ul a').click(function()
		{
			$('.tabs_ul li').removeClass('selected')
			$(this).parents('li').eq(0).addClass('selected');
			
			$('.tab_container:visible').hide();
			$('#' + $(this).attr('href').substr(1) + '.tab_container').show();
			return false;
		});
		
		$('#errors label').click(function()
		{
			lang = $(this).text().match(/\(([a-z]+)\)/i)[1];
			$('.tabs_ul li:not(.selected) a:contains('+lang+')').click();
		});
	}
	// END Tabs
	
	
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
	
	// Checkbox listing
	$('.input .checkbox-list-filter input.filter').keyup(function(e)
	{
		var lis = this.parentNode.nextSibling.getElementsByTagName('li');
		this.nextSibling.checked = false;
		
		for(i in lis)
		{
			var li = lis[i];
			if(typeof(li) == 'object')
			{
				var label = li.getElementsByTagName('label')[0];
				li.style.display = (label.innerHTML.toLowerCase().indexOf(this.value.toLowerCase()) >= 0 ? 'block' : 'none');
			}
		}
	});
	$('.input .checkbox-list-filter .checkboxlist_show_selected').change(function()
	{
		var lis = this.parentNode.nextSibling.getElementsByTagName('li');
		this.previousSibling.value = '';
		
		for(i in lis)
		{
			var li = lis[i];
			if(typeof(li) == 'object'){ li.style.display = (!this.checked || li.childNodes[0].checked ? 'block' : 'none'); }
		}
	});
	
	$('.checkbox-list :checkbox').bind('change', function()
	{
		var $c = $(this).parent().parent().prev();
		$c.val(parseInt($c.val())+(this.checked ? 1 : -1));
	});
	// END Checkbox listing

	$(':checkbox.pages[name=navigation]').bind('load change', function(){ $(this).is(':checked') ? $('#id_parent_id').removeAttr('disabled') : $('#id_parent_id').attr('disabled', 'disabled'); }).trigger('load');
	
	// Listing movers
	$('.movers')
		.children('.icon.arrow_up').click(function()
		{
			var $tr = $(this).parents('tr:eq(0)');
			var $tr_prev = $tr.prev();
			var $order_index = $tr.find('.order_index');
			var order_index = $order_index.val();
			
			if($tr_prev.length)
			{
				$tr.toggleClass('odd').insertBefore($tr_prev.toggleClass('odd'));
				var $prev_index = $tr_prev.find('.order_index');
				var new_index = $order_index.val($prev_index.val()).val();
				$prev_index.val(order_index);
				order_index = new_index;
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
				$tr.toggleClass('odd').insertAfter($tr_next.toggleClass('odd'));
				var $next_index = $tr_next.find('.order_index');
				var new_index = $order_index.val($next_index.val()).val();
				$next_index.val(order_index);
				order_index = new_index;
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
							var new_index = $order_index.val($prev_index.val()).val();
							$prev_index.val(order_index);
							order_index = new_index;
						}
						if($tr_next.length && event.pageY > $tr_next.offset().top)
						{
							$tr.toggleClass('odd').insertAfter($tr_next.toggleClass('odd'));
							var $next_index = $tr_next.find('.order_index');
							var new_index = $order_index.val($next_index.val()).val();
							$next_index.val(order_index);
							order_index = new_index;
						}
						
					})
					.bind('mouseup.listing_order', function()
					{
						$tr.removeClass('moving');
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
			complete: function(){ humanMsg.displayMsg(__globals.ERRORS.listing_saved); }
		});
		
		return false;
	});
	// END Listing movers
	
	
	$('.file-type + .input :checkbox').click(function() { $(this).parent().siblings('.file-type').slideToggle(); });

	// Croppables
	var $croppables = $('.croppable');
	if($croppables.length)
	{
		var $frame = $('<iframe id="upload_frame" name="upload_frame" src="about:blank" style="width: 0px; height: 0px; border: 0px solid #000;"></iframe>').appendTo($('body'));
		
		$croppables.find('img.crop-img').hide();
		$('.upload', $croppables[0]).click(function()
		{
			var $this = $(this);
			if($this.parents('.input:eq(0)').find('.file_field input').val().length <= 0){ return false; }
			
			$this.val('Uploading ...').addClass('uploading').parents('form:eq(0)').attr('target', $frame.attr('name'))
			
			// needs timeout to work after submit
			setTimeout(function(){ $this.attr('disabled','disabled').parents('.croppable').find('input[type=file]').attr('disabled','disabled'); }, 10);
		});
		$('input[type=file]', $croppables[0]).change(function(){ $('input[name^=upload_croppable]:hidden', $croppables[0]).fadeIn(); });
		
		top.croppable = function(field_name, image)
		{
			var $container = $('#id_'+field_name).parents('.croppable');
			
			// needs timeout to work everytime
			setTimeout(function()
			{
				$container
					.find('input[type=file]').removeAttr('disabled').val('').end()
					.find('input[name^=upload_croppable]').fadeOut().val('Upload').removeAttr('disabled').end()
					.find('input[name^=upload_delete]').unbind('click')
						.click(function(){ $container.find('.jcrop-holder, .preview').slideToggle(); }).parent().show();
				
				$container.parents('form:eq(0)').removeAttr('target');
			}, 10);
			
			$img = $container.find('img:eq(0)').hide().attr('src', __rootPath + 'public/files/'+image);
			
			if('Jcrop' in $img[0])
			{
				delete $img[0].Jcrop; // delete must be on new line!!!
				$container.find('.jcrop-holder').remove();
			}
			
			$img.show().unbind('load').load(function()
			{
				var $img = $(this).css('width', '');
				var maxWidth 	= parseInt($img.css('maxWidth') || $img.css('max-width'));
				var maxHeight 	= parseInt($img.css('maxHeight') || $img.css('max-height'));
				
				$img.css('maxWidth', '').css('maxHeight', '');
				var naturalWidth = $img[0].naturalWidth || $img[0].width;
				var naturalHeight = $img[0].naturalHeight || $img[0].height;
				$img.css('maxWidth', maxWidth).css('maxHeight', maxHeight);
				
				var iw = $img.width();
				var ih = $img.height();
				var iwk = iw / naturalWidth;
				var ihk = ih / naturalHeight;
				
				var $preview = $container.find('.preview').show().find('img').attr('src', __rootPath + 'public/files/'+image);
				var $preview_cnt = $preview.parent().click(function(){ return false; });
				var cw = $preview_cnt.width();
				var ch = $preview_cnt.height();
				
				var coords = { x: 0, y: 0, x2: 0, y2: 0 };
				var $coord_x = $img.nextAll('.coord-x');
				var $coord_y = $img.nextAll('.coord-y');
				var $coord_x2 = $img.nextAll('.coord-x2');
				var $coord_y2 = $img.nextAll('.coord-y2');
				
				if($coord_x.val() == ''){ coords.x = iw/2-cw/2; $coord_x.val(Math.floor(coords.x/iwk)) }else{ coords.x = $coord_x.val()*iwk; }
				if($coord_y.val() == ''){ coords.y = ih/2-ch/2; $coord_y.val(Math.floor(coords.y/ihk)) }else{ coords.y = $coord_y.val()*ihk; }
				if($coord_x2.val() == ''){ coords.x2 = iw/2+cw/2; $coord_x2.val(Math.floor(coords.x2/iwk)) }else{ coords.x2 = $coord_x2.val()*iwk; }
				if($coord_y2.val() == ''){ coords.y2 = ih/2+ch/2; $coord_y2.val(Math.floor(coords.y2/ihk)) }else{ coords.y2 = $coord_y2.val()*ihk; }
				
				
				$img.Jcrop(
				{
					setSelect: [ coords.x, coords.y, coords.x2, coords.y2 ],
					onSelect: function(coords){ $coord_x.val(coords.x); $coord_y.val(coords.y); $coord_x2.val(coords.x2); $coord_y2.val(coords.y2); },
					onChange: function(coords)
					{
						cwk = coords.w*iwk;
						chk = coords.h*ihk;
						
						$preview.css(
						{
							width: Math.round( cw*(iw/(cwk)) ) + 'px',
							height: Math.round( ch*(ih/(chk)) ) + 'px',
							marginLeft: '-' + Math.round(coords.x*iwk*(cw/cwk)) + 'px',
							marginTop: '-' + Math.round(coords.y*ihk*(ch/chk)) + 'px'
						});
					},
					boxWidth: maxWidth, 
					boxHeight: maxHeight,
					aspectRatio: ($img.is('.keep-ratio') ? cw/ch : 0)
				});
			});
		}
	}
		

	// User display name
	var display_name_custom = __globals['DISPLAY_NAMES']['custom'];
	$('#id_display_name').change(function()
	{
		if($(this).val() == display_name_custom){ $('#nick_name').show(); }
		else { $('#nick_name').hide(); }
	});
	if($('#id_display_name').val() == display_name_custom){ $('#nick_name').show(); }
	// END User display name
	
	if($('.main_form').height() > $('.sidebar_form').height()){ $('.sidebar_form').height($('.main_form').height()); }
	
	$('#cnt_inn').css('min-height', $('#sidebar').height() + 70);
	
	// ############# CUSTOMS
	
	
	
	
});


