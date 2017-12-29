/**
 * Update the select list showing teachers corresponding to the selected class and section
**/
function updateTeacherList()
{
	var class_id = $('#class_id').val();
	var section_id = $('#section_id').val();

	if(class_id==0)
	{
		$('#teacher_id').html('<option value="0">--Select Class and Section First--</option>');
	}
	else if(section_id==0)
	{
		$('#teacher_id').html('<option value="0">--Select Section First--</option>');
	}
	else
	{
		var query_url = $('#teacher_ajax').val();
		$('#teacher_id').html('<option value="0">Loading...</option>');
		$.get(query_url+"?class_id="+class_id+"&section_id="+section_id, 
			function(data, status) 
			{
					if(status)
		    	{
		    		var section_options = new Object(); // or just {}
		    		data = $.parseJSON(data);
					
					for (var i in data)
					{
						section_options[data[i]['teacher_id']] = data[i]['teacher_name'];
					}

		    		$('#teacher_id').html(getStaticSelectList(section_options));
		    	}
		      	
	    	});
	}
}