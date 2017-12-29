<script>
$(function(){

	$('#group_type').change(function(e)
	{

		var url = $('#current_url').val();
			url += '?group_type='+$(this).val()+"&group_id="+$('#group_id').val();
		window.location.replace(url);
	});

});
</script>