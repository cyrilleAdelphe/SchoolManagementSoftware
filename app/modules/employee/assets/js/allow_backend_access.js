<script>

$(document).ready(function()
{

	$('#eton_allow_backend_access_checkbox').change(function(e)
	{

		if ($(this).is(':checked')) 
		{
			$('#eton_allow_backend_access').show();
		}
		else
		{
			$('#eton_allow_backend_access').css('display', 'none');
		}

	});

});

</script>