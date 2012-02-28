function FwurksTree(_dom)
{
	var rootUL;
	var actions;
	
	var switch_class 	= 'switch';
	var empty_class 	= 'empty';
	var opened_class 	= 'active';
	
	function init(_dom)
	{
		actions = __globals.ACTIONS; 
	
		rootUL = _dom;
		
		$('li a.'+switch_class+', li a.'+empty_class, rootUL).css('display', 'block').bind('click.fwurks_tree', {opened_class: opened_class}, function(event)
		{
			if(!$(this).is('.empty')){ $(this).toggleClass(event.data.opened_class).siblings('ul').slideToggle(100); } 
		});
		
		$('li:last-child', rootUL).addClass('last');
		
		rootUL.jTree(
		{
//			level			: 3,
			handle			: ' > a.title',
			handle_parent	: 'li:eq(0)',
			rootUL			: rootUL,
			
			onend: function(li, old_parent_li)
			{
				li.children('input:hidden').val((li.parents('li:eq(0)').attr('rel') || 'id_0').substr('id_'.length));
			
				if(old_parent_li.find('ul:eq(0) > li:last').length)
				{
					old_parent_li.addClass('open')
						.find('a.switch:eq(0)').addClass('switch').addClass('active').removeClass('empty');
				}
				else
				{
					old_parent_li.removeClass('open')
						.find('a.switch:eq(0)').removeClass('switch').removeClass('active').addClass('empty');
				}
				
				parent_li = li.parents('li:eq(0)');
				parent_li.addClass('open').children('a.empty').attr('class', 'switch active').css('display', 'block');
				
				li.add(parent_li);
				
				this.rootUL.find('li').removeClass('last');
				this.rootUL.find('li:last-child').addClass('last');
				if(li.nextAll().length < 2){ li.addClass('last'); }
			}
		});
		
		expand_all();
		
		add_button('save', 		'save_tree_order', 	'#save_order=1', 	function(){ save(); 		return false; });
		add_button('expand', 	'expand_tree', 		'#expand_tree', 	function(){ expand_all(); 	return false; });
		add_button('collapse', 	'collapse_tree', 	'#collapse_tree', 	function(){ collapse_all();	return false; });
	}
	
	function add_button(_class, _action, _href, _bind_function)
	{
		$('<a class="button '+_class+'" title="'+actions[_action]+'" href="'+_href+'"><em class="icon '+_class+'"></em><span>'+actions[_action]+'</span></a>')
			.insertBefore('#actions .menu .cleaner').bind('click.fwurks_tree', _bind_function);
	}

	function expand_all()
	{
		$('li a.'+switch_class, rootUL).addClass(opened_class);
		$('li ul:hidden', rootUL).slideDown(100);
	}
	
	function collapse_all()
	{
		$('li a.'+switch_class, rootUL).removeClass(opened_class);
		$('li ul:visible', rootUL).slideUp(100);
	}
	
	function save()
	{
		$.ajax(
		{
			type	: 'POST',
			url		: "?save_order=1",
			data	: serialize(),
			success	: function(data){ if(data == 1){ alert(__globals.ERRORS.tree_saved); } }
		});
	}
	
	function serialize()
	{
		return $('li input[name^="tree_order"]', rootUL).serialize();
	}
	
	init(_dom);
	
	return {
		// public
	}
};

$(function()
{
	tree = new FwurksTree($('ul.tree'));
});
