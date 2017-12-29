/**
 * Update the select list showing section corresponding to the selected class
**/
function updateSectionList(default_section, default_option)
{
	var class_id = $('#class_id').val();

	if(class_id==0 || class_id=='')
	{
		// TODO: this will make it difficult, pass this as a function parameter when needed (i.e at page loading)
		class_id = $('#default_class').val();
	}

	if(class_id==0 || class_id=='')
	{
		$('#section_id').html('<option value="0">--Select Class First--</option>');
	}
	else
	{
		var query_url = $('#section_ajax').val();
		$('#section_id').html('<option value="0">Loading...</option>');
		$.get(query_url+"?class_id="+class_id, 
			function(data, status) 
			{
		    	if(status)
		    	{
		    		var section_options = new Object(); // or just {}
		    		data = $.parseJSON(data);
					
					for (var i in data)
					{
						section_options[data[i]['id']] = data[i]['section_code'];
					}

					if(typeof(default_section) == 'undefined')
						default_section = '';
		    		$('#section_id').html(getStaticSelectList(section_options, default_section, default_option));
		    	}
		      	
	    	});
	}
}
