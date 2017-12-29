/**
 * Update the select list showing section corresponding to the selected class
 **/
function updateSubjectList(default_subject)
{
	var class_id = $('#class_id').val();
	var section_id = $('#section_id').val();

	if(class_id==0 || class_id=='')
	{
		// TODO: this will make it difficult, pass this as a function parameter when needed (i.e at page loading)
		class_id = $('#default_class').val();
	}

	if(section_id==0 || section_id=='')
	{
		// TODO: this will make it difficult, pass this as a function parameter when needed (i.e at page loading)
		section_id = $('#default_section').val();
	}

	if(class_id==0 || section_id==0)
	{
		$('#subject_id').html('<option value="0">--Select Class/Section First--</option>');
	}
	else
	{
		var query_url = $('#subject_ajax').val();
		query_url += "?class_id="+class_id+"&section_id="+section_id;
		$('#subject_id').html('<option value="0">loading...</option>');
		$.get(query_url, 
			function(data, status) 
			{
		    	if(status)
		    	{

		    		var subject_options = new Object();
		    		console.log(data);
		    		data = $.parseJSON(data);
		    		for (var i in data)
					{
						subject_options[data[i]['id']] = data[i]['subject_code'];
					}

					if(typeof(default_subject) == 'undefined')
						default_subject = '';

					$('#subject_id').html(getStaticSelectList(subject_options, default_subject));
		    	}
		      	
	    	});
	}
}
