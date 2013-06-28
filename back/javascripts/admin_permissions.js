$(function()
{
	ul = $('.permissions')
	
	$('[rel=admin_permissions_parent]', ul).change(function(){
		if(!this.checked){ $(this).parents('li:eq(0)').find('[rel=admin_permissions_child]').removeAttr('checked'); }
	});
	$('[rel=admin_permissions_child]', ul).change(function(){
		if(this.checked){ $(this).parents('li:eq(1)').find('[rel=admin_permissions_parent]').attr('checked', 'checked'); }
	});
});