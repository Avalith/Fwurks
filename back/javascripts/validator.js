function Validator(form)
{
	var error_labels = __labels.__globals.DATABASE_ERRORS;
	var field_labels = __labels.__globals.DATABASE_FIELDS;
	
	form.submit(function()
	{
		$('[rel^=validate]', form).blur();
		if($('.error', form).length){ return false; } 
	});
	
	$('[rel^=validate]', form).blur(function()
	{
		field = $(this);
		field_name = field.attr('name');
		validations = field.attr('rel').substr('validate '.length).split(' ');
		
		parent = field.parents('.input:eq(0)');
		parent.find('.error').remove();
		
		error = '';	
		for(v in validations)
		{
			eval('var check = check_'+validations[v]);
			
			if(error = check(field))
			{
				$('<div class="error"></div>').appendTo(parent.addClass('invalid')).text(error_labels[error]);
				
				if( (tab_ul = field.parents('.inner:eq(0)').siblings('.tabs_ul')).length )
				{
					tab_ul.children(':has(a[href=#'+ field.parents('.tab_container:eq(0)').attr('id') +'])').addClass('invalid');
				}
				
				break;
			}
			else
			{
				parent.removeClass('invalid').children('.error').remove()
				
				if( (tab_ul = field.parents('.inner:eq(0)').siblings('.tabs_ul')).length )
				{
					tab_ul.children(':has(a[href=#'+ field.parents('.tab_container:eq(0)').attr('id') +'])').removeClass('invalid');
				}
			}
		}
	});
	
	
	function check_noempty(field)
	{
		if(field.val()){ return; }
		return 'cant_be_empty';
	}
	
	function check_chlist_noempty(field)
	{
		if(field.find(':checked').length){ return; }
		return 'cant_be_empty';
	}
	
	function check_maxlength(field)
	{
		if(field.val().length <= field.attr('maxlength')){ return };
		return 'too_long';
	}
	
	function check_numeric(field)
	{
		if( !field.val() || field.val() == parseInt(field.val()) ){ return; }
		return 'numeric';
	}
	
	function check_alphanumeric(field)
	{
		return;
		if( !field.val().match(/[^\w]/i) ){ return; }
		return 'alphanumeric'; 
	}
	
	function check_email(field)
	{
		return;
		return 'email';
	}
}