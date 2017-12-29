<script>
	$(document).ready(function()
	{
		$('.eton_user_group_all').click(function(e)
		{
			var boxes = $(this).parent().parent().find('.eton_user_group');
			
			if($(this).is(':checked'))
			{
				/*$( "li" ).each(function( index ) {
  console.log( index + ": " + $( this ).text() );
});*/
				boxes.each(function(index)
				{
					$(this).prop('checked', 'checked');
				});
			}
			else
			{
				boxes.each(function(index)
				{
					$(this).prop('checked', false);
				});
			}
		});

		$('.eton_admin_group_all').click(function(e)
		{
			var boxes = $(this).parent().parent().find('.eton_admin_group');
			if($(this).is(':checked'))
			{
				boxes.each(function(index)
				{
					$(this).prop('checked', 'checked');
				});
			}
			else
			{
				boxes.each(function(index)
				{
					$(this).prop('checked', false);
				});
			}
		});
	});
</script>