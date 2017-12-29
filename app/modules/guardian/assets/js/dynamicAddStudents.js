// event trigger for the remove student button
function removeStudent(element) {
  $(element).parent('.multi-field').remove();
}

// event trigger for select button after searching student
function findIdSelect(username, element) {
  var wrapper = $('#wrapper');
  var html = '<div class="multi-field">';
  html += '<label>Student Username: </label>'
  html += '<input type="text" name="student_username[]">';
  html += '<label>Relationship: </label>'
  html += '<input type="text" name="relationship[]">';
  html += '<button type="button" class="remove-field" onclick="removeStudent(this)">Remove</button>';
  html += '</div>';
  $(html).clone(true).appendTo(wrapper).find('input[name="student_username[]"]').val(username).focus();
}