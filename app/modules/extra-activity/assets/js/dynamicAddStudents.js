// event trigger for the remove student button
function removeStudent(element) {
  $(element).parent('.multi-field').remove();
}

// event trigger for select button after searching student
function findIdSelect(username, element) {
	var wrapper = $('#wrapper');
  
	var html = '<div class="multi-field row">';
	html += '<div class="col-sm-5">';
	html += '<div class="form-group">';
	html += '<input id="title" name = "student_username[]" class="form-control" type="text" placeholder="Enter student\'s code">';
	html += '</div>';
	html += '</div>';

	html += '<div class="col-sm-5">';
	html += '<div class="form-group">';
	html += '<input id="title" name = "remarks[]" class="form-control" type="text" placeholder="Enter remarks or position">';
	html += '</div>';
	html += '</div>';
	  
	html += '<button type="button" class="remove-field" onclick="removeStudent(this)">Remove</button>';
	html += '</div>';
  $(html).clone(true).appendTo(wrapper).find('input[name^=student_username]').val(username).focus();
}