$(function()
{
	var ajax_url = $('#ajax_url').val();

	$('#ajax_search').click(function()
	{
		
		var unique_school_roll_number = $('#ajax_input_for_unique_school_roll_number').val();
		var class_id = $('#ajax_input_for_class').val();
		var student_name = $('#ajax_input_for_student_name').val();

		$.ajax({
				            "url": ajax_url,
				            "data": {"unique_school_roll_number" : unique_school_roll_number, "class_id" : class_id, "student_name" : student_name},
				            "method": "GET"
	          			}).done(function(data) {
				 				
				 				console.log(data);
				 				$('#div_for_ajax_results').html(data);
				});
	});

	//script for allowing backend access checkbox
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

	//////// script to add students to guardians //////////////////
	$('#yourContainer').on('click', '#approve', function(){
    //your code here..
});


});