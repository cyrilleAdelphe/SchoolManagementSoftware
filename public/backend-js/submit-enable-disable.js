$(function()
{

	$(document).on('click', '.submit-enable-disable', function(e)
	{
		//e.preventDefault();
		var form_id = $(this).attr('related-form');
		var name = $(this).attr('name');
		var value = $(this).val();
		$(this).attr('disabled', true);
		$('#' + form_id).append('<input type = "hidden" name = "' + name + '" value = "' + value + '">');
		$('#' + form_id).submit();
		
	})

});