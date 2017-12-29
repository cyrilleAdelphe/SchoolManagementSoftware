$(function()
{

	$('.paginate_list').change(function()
	{
		var url = $('#current_url').val() + '?paginate=' + $(this).val();

		window.location = url;
	});

})