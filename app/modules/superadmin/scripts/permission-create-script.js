<script>
$(function(){

	$('#module_id').change(function(e)
	{

		var url = $('#current_url').val();
			url += '?module_id='+$(this).val();
		window.location.replace(url);
	});

});
</script>