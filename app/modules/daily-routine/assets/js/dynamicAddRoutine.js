$('.multi-field-wrapper').each(function() {
    var $wrapper = $('.multi-fields', this);
    $(".add-field", $(this)).click(function(e) {
    	// $('.multi-field:first-child', $($wrapper)).clone(true).appendTo($wrapper).find('input').val('').focus();

        var multiField = $(this).closest('.multi-field-wrapper').find('.multi-fields .multi-field').last();
        var newField = multiField.clone();
        newField.find('.timepicker').timepicker({
            minuteStep: 5,
            showInputs: false
        });

        function copyBinders(selector) {
            $.each($._data($(selector).get(0), 'events'), function() {
              // iterate registered handler of remove-field
              $.each(this, function() {
                newField.find(selector).bind(this.type, this.handler);
              });
            });
        }

        // we bind the required handlers from previous
        copyBinders('.remove-field');

        autocompleteSubjects(newField.find('input[name^="subject"]'));
        autocompleteTeachers(newField.find('input[name^="teacher"]'));
        
        newField.appendTo(multiField.parent()).find('input').val('');//.focus();

        $('input[name^="start_time"]').last().val(multiField.find('input[name^="start_time"]').first().val());
        $('input[name^="end_time"]').last().val(multiField.find('input[name^="end_time"]').first().val());

        


    });
    $('.multi-field .remove-field', $wrapper).click(function() {
        if ($('.multi-field', $wrapper).length > 1) {
        	//$(this).parent('.multi-field').remove();
            $(this).closest('.multi-field').remove();
        }
    });

});