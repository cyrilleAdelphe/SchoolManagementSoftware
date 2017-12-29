$(document).ready(function()
{
	$('#PraSave').click(function(e)
	{
		e.preventDefault();
		$('#backendForm').append('<input type = "hidden" name = "formAction" value = "s">');
		$('#backendForm').submit();
	});

	$('#PraSaveAndNew').click(function(e)
	{
		e.preventDefault();

		$('#backendForm').append('<input type = "hidden" name = "formAction" value = "sn">');
		$('#backendForm').submit();
	});

	$('#PraSaveAndClose').click(function(e)
	{
		e.preventDefault();

		$('#backendForm').append('<input type = "hidden" name = "formAction" value = "sc">');
		$('#backendForm').submit();
	});

	$('#PraUpdate').click(function(e)
	{
		e.preventDefault();

		$('#backendForm').append('<input type = "hidden" name = "formAction" value = "u">');
		$('#backendForm').submit();
	});

	$('#PraUpdateAndClose').click(function(e)
	{
		e.preventDefault();

		$('#backendForm').append('<input type = "hidden" name = "formAction" value = "u">');
		$('#backendForm').submit();
	});

	$('#PraPurge').click(function(e)
	{
		e.preventDefault();
		var url = $(this).attr('href');
		var queries = $('#backendListForm').attr('action');
		    url += queries;
		$('#backendListForm').attr('action', url);
		$('#backendListForm').submit();

	});

	$('#PraDelete').click(function(e)
	{
		e.preventDefault();
		var url = $(this).attr('href');
		var queries = $('#backendListForm').attr('action');
		    url += queries;
		$('#backendListForm').attr('action', url);
		$('#backendListForm').submit();
	});

	$('#PraCheckAll').click(function(e)
	{
		if($(this).is(':checked'))
		{
			$('.checkbox_id').prop('checked', true);
		}
		else
		{
			$('.checkbox_id').prop('checked', false);
		}
	});
});	