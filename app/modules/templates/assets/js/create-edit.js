<script>

$(document).ready(function(e)
{

	$('.position_id').change(function(e)
	{

		e.preventDefault();

		if($(this).is(':checked'))
		{
			$(this).parent().find('.eton_sort_order_disabled').attr('disabled', false)
		}
		else
		{
			$(this).parent().find('.eton_sort_order_disabled').attr('disabled', true)
		}

	});

});
</script>