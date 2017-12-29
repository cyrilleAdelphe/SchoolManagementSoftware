@extends('backend.'.$role.'.main')

@section('content')
	<form method="post" action="{{URL::route('exam-marks-update-post')}}" id="backendForm">
	<div class="row">
	<div class="col-sm-2">
      <div class="form-group">
        <label>Select session</label>
        {{HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'session_id', Input::get('session_id', HelperController::getCurrentSession() ))}}
      </div>
    </div>
    <div class="col-sm-2">
      <div class="form-group">
        <label>Select Exam</label>
        <select name="exam_id" id="exam_id" class="form-control">
					<option value = "0">-- Select Session First --</option>
				</select>
			</div>
    </div>

    <div class="col-sm-2">
      <div class = "form-group @if($errors->has("class_id")) {{"has-error"}} @endif">
				<label for = "class_id">Select class</label>
				<select name="class_id" id="class_id" class="form-control">
					<option value = "0">-- Select Exam First --</option>
				</select>
				@if($errors->has('class_id'))
          <span class = "form-error"> 
          	{{ $errors->first('class_id') }} 
          </span>
        @endif
			</div>
			<input type = "hidden" name = "class_id" value = "{{Input::get('class_id', 0)}}">
    </div>

    <div class="col-sm-3">
      <div class="form-group @if($errors->has("section_id")) {{"has-error"}} @endif">
	      <label>Select section</label>
	      <select name="section_id" id="section_id" class="form-control">
					<option value="0">--Select Class First--</option>
					@if(Input::get('section_id', 0) != 0)
					<option value = "{{Input::get('section_id')}}" selected>{{HelperController::pluckFieldFromId('Section', 'section_code', Input::get('section_id'))}}</option>
					@endif
				</select>
	    </div>
    </div>

    <div class="col-sm-3">
      <div class="form-group @if($errors->has("subject_id")) {{"has-error"}} @endif">
	      <label>Select subject</label>
	      <select name="subject_id" id="subject_id" class="form-control">
					<option value="0">--Select Class/Section First--</option>
					@if(Input::get('subject_id', 0) != 0)
					<option value = "{{Input::get('section_idsubject_id')}}" selected>{{HelperController::pluckFieldFromId('Subject', 'subject_code', Input::get('subject_id'))}}</option>
					@endif
				</select>
	    </div>
    </div>
  </div><!-- row ends -->

	<div class="row">
    <div class="col-sm-12">
      <h4 class="text-red">Class {{HelperController::pluckFieldFromId('Classes', 'class_name', Input::get('class_id', 0))}} 
	      {{HelperController::pluckFieldFromId('Section', 'section_code', Input::get('section_id', 0))}} 
	      @if($last_updated_data)
	      <small>Last updated by : {{$last_updated_data->updated_by}} at 
		      <span class="text-green">
		      	{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $last_updated_data->updated_at)->format('d M Y')}}
	      	</span>
      	</small>
      	@endif
      </h4>
      	
	      <table id="pageList" class="table table-bordered table-striped">
	        <thead>
	          <tr>
	            <th>Roll Number</th>
	            <th>Name</th>
	            <th>Marks  FM: {{$full_marks_pass_marks->full_marks}} PM: {{$full_marks_pass_marks->pass_marks}} <input type = "hidden" id = "full_marks" value = "{{$full_marks_pass_marks->full_marks}}">
	            <input type = "hidden" id = "pass_marks" value = "{{$full_marks_pass_marks->pass_marks}}">
	            </th>
	            @if($full_marks_pass_marks->practical_full_marks)
	            <th>Marks  FM: {{$full_marks_pass_marks->practical_full_marks}} PM: {{$full_marks_pass_marks->practical_pass_marks}} <input type = "hidden" id = "practical_full_marks" value = "{{$full_marks_pass_marks->practical_full_marks}}">
	            <input type = "hidden" id = "practical_pass_marks" value = "{{$full_marks_pass_marks->practical_pass_marks}}">
	            </th>
	            @endif
	            <th>Comments</th>
	          </tr>
	        </thead>
	        <tbody>
	        	@if($student_marks==='')
	        		<tr>
	        			<td>SELECT CLASS/SECTION/SUBJECT FIRST</td>
	        		</tr>
	        	@elseif(sizeof($student_marks)==0)
	        		<tr>
	        			<td>NO STUDENTS IN THE CLASS</td>
	        		</tr>
	        	@else
	        		
	        		@foreach($student_marks as $student)
	        			<tr>
	        			<td>{{$student->current_roll_number}}</td>
<td>{{$student->student_name}} {{$student->last_name}}</td>
	        			<td>
	        				<input name="marks[]" class="form-control obtained_marks" type="number" value="{{$student->marks}}">
	        			</td>
	        			@if($full_marks_pass_marks->practical_full_marks)
	        			<td>
	        				<input name="practical_marks[]" class="form-control practical_obtained_marks" type="number" value="{{$student->practical_marks}}">
	        			</td>
	        			@else
	        				<input name="practical_marks[]" class="form-control practical_obtained_marks" type="hidden" value="{{$student->practical_marks}}">
	        			@endif
	        			<td>
	        				<input name="comments[]" class="form-control" type="text" value="{{$student->comments}}">
	        			</td>
	        			<input name="student_id[]" type="hidden" value="{{$student->student_id}}">
	        			<input name="subject_id[]" type="hidden" value="{{$student->subject_id}}">
	        			</tr>
	        		@endforeach
	        	@endif
	        </tbody>
	      </table>
	      
	      <input type="hidden" id="section_ajax" value="{{URL::route('ajax-attendance-get-class-section')}}" />

			  <input type="hidden" id="subject_ajax" value="{{URL::route('ajax-subject-get-subjects')}}" />

			  <input type="hidden" name= "default_class" id="default_class" value="{{Input::has('class_id')?Input::get('class_id'):''}}" />

				<input type="hidden" name= "default_section" id="default_section" value="{{Input::has('section_id')?Input::get('section_id'):''}}" />

				<input type="hidden" name= "default_subject" id="default_subject" value="{{Input::has('subject_id')?Input::get('subject_id'):''}}" />

				{{Form::token()}}

	      <div class="form-group">
	        <button type="submit" class="btn btn-success submit-enable-disable" related-form="backendForm" @if(AccessController::checkPermission('exam-marks', 'can_edit') == false) disabled @endif >Save</button>
	      </div>
	    
    </div>                  
  </div>
  <input type = "hidden" id = "ajax_get_exam_ids_from_session_id" value = "{{URL::route('ajax-get-exam-ids-from-session-id')}}">

  </form>

  
@stop

@section('custom-js')

<script src="{{ asset('backend-js/getStaticSelectList.js') }}" type="text/javascript"></script>
<script src="{{ asset('backend-js/submit-enable-disable.js') }}" type="text/javascript"></script>

<script src="{{ asset('backend-js/updateSectionList.js') }}" type="text/javascript"></script>

<script src="{{ asset('backend-js/updateSubjectList.js') }}" type="text/javascript"></script>

<script>
	
  //$(document).on('change', '#class_id', updateSectionList);
  //$(document).on('change', '#section_id', updateSubjectList);
  $(document).on('change', '#subject_id', updateStudentTable);
  
	/**
	 * Update the student table corresponding to the selected class, section and subject
	**/
	function updateStudentTable()
	{
		var class_id = $('#class_id').val();
		var section_id = $('#section_id').val();
		var subject_id = $('#subject_id').val();
		var exam_id = $('#exam_id').val();
		var session_id = $('#session_id').val();
		if(class_id!=0 && section_id!=0 && subject_id!=0)
		{
			var current_url = $('#current_url').val();
			current_url += '?class_id=' + class_id + '&section_id=' + section_id + '&subject_id=' + subject_id + '&exam_id=' + exam_id  + '&session_id=' + session_id;
			window.location.replace(current_url);
		}
	}

	function updateClassList(session_id, default_class)
  {
  	if (typeof(session_id) == 'undefined') {
    	session_id = $('#session_id').val();
  	}
    if (session_id == 0) return;
    $('#class_id').html('<option value="0">Loading...</option>');
    $.ajax( {
                      "url": "{{URL::route('ajax-get-related-classes')}}",
                      "data": {"session_id" : session_id, 'default_class_id' : default_class},
                      "method": "GET"
                      } ).done(function(data) {
                  $('#class_id').html(data);
                  if (typeof(default_class) != 'undefined')
                  {
                  	$('#class_id').val(default_class);
                  }
                });
  }

  function updateExamList(default_exam) {
  	var session_id = $('#session_id').val();
    var url = $('#ajax_get_exam_ids_from_session_id').val();

    if (session_id == 0) return;
    $('#exam_id').html('<option value="0">Loading...</option>');
    $.ajax( {
	    "url": url,
	    "data": {"session_id" : session_id},
	    "method": "GET"
    } ).done(function(data) {
      $('#exam_id').html(data);
      if (typeof default_class != 'undefined') {
      	$('#exam_id').val(default_exam);
      }
    });
  }

  $(function() {
  	updateExamList("{{ Input::get('exam_id', 0) }}");
  	updateClassList("{{ Input::get('session_id', 0) }}", "{{ Input::get('class_id', 0) }}");
  	updateSectionList("{{ Input::get('section_id', 0) }}");
  	updateSubjectList("{{ Input::get('subject_id', 0)}}");
  });

	$('#exam_id').change(function() {
		updateClassList();
		$('#pageList').html('');
	});

	$('#session_id').change(function() {
		updateExamList();
		$('#pageList').html('');
	});

	$('#class_id').change(function()
	    {
	      var class_id = $(this).val();
	      	$('#section_id').html('<option value="0">Loading...</option>');
	        $.ajax( {
	                      "url": "{{URL::route('ajax-get-related-sections')}}",
	                      "data": {"class_id" : class_id},
	                      "method": "GET"
	                      } ).done(function(data) {
	                  $('#section_id').html(data);
	                  $('#pageList').html('');
	                });   
	      });
		
	    $("#section_id").bind('change', updateSubjectList);
	    $('.obtained_marks').focusout(function(e)
	    {
	    	checkValidityOfMarks($(this));
	    });

	    $('.obtained_marks').change(function(e)
	    {
	    	//if(e.which == 13 || e.keyCode == 13)
	    	//{
	    		checkValidityOfMarks($(this));
	    	//}
	    });

	    $('.practical_obtained_marks').change(function(e)
	    {
	    	//if(e.which == 13 || e.keyCode == 13)
	    	//{
	    		checkValidityOfPracticalMarks($(this));
	    	//}
	    });

function checkValidityOfMarks(marks)
{
	if (Math.trunc(marks.val()) > Math.trunc($('#full_marks').val()))
	    	{
	    		alert('obtained marks cannot be greated than ' + $('#full_marks').val());
	    		$(marks).val($('#full_marks').val());
	    	}

	    	if(marks.val() < 0)
	    	{
	    		alert('obtained marks cannot be less than 0');
	    		$(marks).val(0);
	    	}
}

function checkValidityOfPracticalMarks(marks)
{
	console.log($('#practical_full_marks').val());
	if (Math.trunc(marks.val()) > Math.trunc($('#practical_full_marks').val()))
	    	{
	    		alert('obtained marks cannot be greated than ' + $('#practical_full_marks').val());
	    		$(marks).val($('#practical_full_marks').val());
	    	}

	    	if(marks.val() < 0)
	    	{
	    		alert('obtained marks cannot be less than 0');
	    		$(marks).val(0);
	    	}
}
</script>
@stop