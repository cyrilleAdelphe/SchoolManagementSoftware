@extends('backend.'.$role.'.main')

@section('custom-css')
 <link href="{{asset('sms/plugins/daterangepicker/daterangepicker-bs3.css')}}" rel="stylesheet" type="text/css" />
 <link href="{{asset('sms/plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css" />
 <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

@stop

@section('content')

<div class = 'content'>
	<form method = "post" action = "{{URL::route('billing-discount-create-post')}}"  class = "form-horizontal" id = "backendForm">

		<div class = 'form-group @if($errors->has("organization_id")) {{"has-error"}} @endif'>
      <label for = 'organization_id'>Organization:</label>
      <?php $organizations = BillingDiscountOrganization::where('is_active', 'yes')
                                                          ->select('id', 'organization_name')
                                                          ->lists('organization_name', 'id'); ?>

      <select name = "organization_id">
        @foreach($organizations as $id => $o)
        <option value = "{{$id}}">{{$o}}</option>
        @endforeach
      </select>
      <span class = 'help-block'>@if($errors->has('organization_id')) {{$errors->first('organization_id')}} @endif</span>
    </div>

    <div class = 'form-group @if($errors->has("discount_name")) {{"has-error"}} @endif'>
			<label for = 'discount_name'>Discount Name:</label>
			<input type = 'text' name = 'discount_name' value= '{{ (Input::old('discount_name')) ? (Input::old('discount_name')) : '' }}' class = 'form-control required'><span class = 'help-block'>@if($errors->has('discount_name')) {{$errors->first('discount_name')}} @endif</span>
		</div>
	
    <div class = "row">
        <div class = "col-md-3">
          <b>Fee Type</b>
        </div>
        <div class = "col-md-3">
          <b>Student Name</b>
        </div>
      
        <div class = "col-md-3">
            <b>Discount %</b>
        </div>
      </div>
		<div id = "billing-discount-fees">

			<div class = "row billing-discount-fee-details">
        <div class = "col-md-3">
					<select class = "billing-discount-fee" name = "fee_id[]">
					 <option value = 'all'>All</option>
          @foreach($fees as $fee_id => $fee_name)
						<option value = "{{$fee_id}}">{{$fee_name}}</option>
					@endforeach
					</select>
        </div>

        <div class = "col-md-3">
          <input type = "text" class = "auto">
          <input type = "hidden" name = "student_id[]" class = "auto-student-id">
        </div>

        
        <div class = "col-md-3">
					<input type = "number" step = "0.01" name = "discount_percent[]">
        </div>

        <div class = "col-md-3">
					<a href = "#" class = "remove btn btn-success">Remove</a>
        </div>
			</div>

		</div>
    <div class = "row">
      <a href = "#" class = "btn btn-default add-more">Add More</a>
    </div>
		
		<input type = 'hidden' name = 'is_active' value = 'yes'>
		<div class="form-group">
			{{Form::token()}}
		<button class="btn btn-success btn-lg btn-flat" type="submit">Create</button>
		</div>
	</form>
	<input type = "hidden" id = "billing-ajax-get-class-list" value = '{{URL::route('billing-ajax-get-class-list')}}'>
  	<input type = "hidden" id = "billing-ajax-get-section-list" value = '{{URL::route('billing-ajax-get-section-list')}}'>
  	<input type = "hidden" id = 'billing-ajax-get-student-select-list' value = '{{URL::route('billing-ajax-get-student-select-list')}}'>
</div>

@stop

@section('custom-js')

<script src = "{{ asset('sms/plugins/jQueryUI/jquery-ui-1.10.3.min.js') }}" type = "text/javascript"></script>
<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "text/javascript"></script>
<script src = "{{ asset('backend-js/validation.js') }}" type = "text/javascript"></script>
<script>
	$(function() 
	{
    var default_dynamic_fields = $('#billing-discount-fees').html();

    var select_html = $('.billing-discount-fee-details').html();

      $(document).on('keyup.autocomplete', '.auto', function(){
        $(this).autocomplete({select: function( event, ui ) 
                {
                 // console.log(ui);
                 $(this).parent().find('.auto-student-id').val(ui.item.id);
                  //console.log(event);
                },
        source: "{{URL::route('ajax-student-id-autocomplete')}}",
        minLength: 3
        
      });
    });


    
		$(document).on('click', '.add-more', function(e)
		{
			e.preventDefault();
			$('#billing-discount-fees').append(default_dynamic_fields);
		});

		$(document).on('click', '.remove', function(e)
		{
			e.preventDefault();
			$(this).parent().parent().remove();
		});

      $(document).on('change', '.academic_session_id', function(e)
      {
      	var currentElement = $(this);
        updateClassList(currentElement);
        updateSectionList(currentElement);
        updateStudentList(currentElement);
        
      });

      $(document).on('change', '.class_id', function(e)
      {
      	var currentElement = $(this);
        updateSectionList(currentElement);
        updateStudentList(currentElement);
        
      });

      $(document).on('change', '.section_id', function(e)
      {
      	var currentElement = $(this);
        updateStudentList(currentElement);
        
      });

      function updateClassList(currentElement)
      {
      	var currentRow = currentElement.parent();
        var session_id = currentRow.find('.academic_session_id').val();
        currentRow.find('.class_id').html('loading...');
        $.ajax
        ({
            'url' : $("#billing-ajax-get-class-list").val(),
            'method' : 'GET',
            'data' : {'academic_session_id' : session_id, 'extra' : ''}
        }).done(function(data)
        {
        	console.log(data);
          currentRow.find('.class_id').html(data);
        });
      }

      function updateSectionList(currentElement)
      {
      	var currentRow = currentElement.parent();
        var class_id = currentRow.find('.class_id').val();
        currentRow.find('.section_id').html('loading...');
        $.ajax
        ({
            'url' : $("#billing-ajax-get-section-list").val(),
            'method' : 'GET',
            'data' : {'class_id' : class_id, 'extra' : ''}
        }).done(function(data)
        {
          currentRow.find('.section_id').html(data);
        });
      }

      function updateStudentList(currentElement)
      {
      	var currentRow = currentElement.parent();
        var session_id = currentRow.find('.academic_session_id').val();
        var class_id = currentRow.find('.class_id').val();
        var section_id = currentRow.find('.section_id').val();
        
        $.ajax
        ({
          'url' : $("#billing-ajax-get-student-select-list").val(),
          'method' : 'GET',
          'data' : {'class_id' : class_id, 'academic_session_id' : session_id, 'section_id' : section_id}
        }).done(function(data)
        {
          currentRow.find('.student_id').html(data);
        });
      }
      
    });
</script>


@stop

