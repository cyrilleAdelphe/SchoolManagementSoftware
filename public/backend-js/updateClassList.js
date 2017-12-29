/**
 * Update the select list showing class corresponding to the academic session
**/
function updateClassList(default_class)
{
	var academic_session_id = $('#academic_session_id').val();

	if(academic_session_id==0 || academic_session_id=='')
	{
		academic_session_id = $('#default_academic_session').val();
	}


	if(academic_session_id==0)
	{
		$('#class_id').html('<option value="0">--Select Session First--</option>');
	}
	else
	{
		var query_url = $('#class_ajax').val();
		$('#class_id').html('<option value="0"> loading... </option>');
		$.get(query_url+"?academic_session_id="+academic_session_id, 
			function(data, status) 
			{
		    	if(status)
		    	{
		    		var class_options = new Object();
		    		data = $.parseJSON(data);
					
					for (var i in data)
					{
						class_options[data[i]['id']] = data[i]['class_code'];
					}

					if(typeof(default_class) == 'undefined')
						default_class = '';
		    		$('#class_id').html(getStaticSelectList(class_options, default_class));
		    	}
		      	
	    	});
	}
}
