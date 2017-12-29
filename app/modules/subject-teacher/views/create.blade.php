@extends('backend.'.$current_user->role.'.main')

@section('custom-css')
	<!-- Theme style -->    
    <link href="{{asset('sms/plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('page-header')
  <h1>
    Assign Teachers
  </h1>
@stop

@section('content')

<div class="tab-pane " id="tab_2">
<div style="margin-bottom: 10px; display: block; clear: both; overflow: hidden">
  <a  href="#" onclick="history.go(-1);" class="btn btn-danger  btn-flat"><i class="fa fa-fw fa-arrow-left"></i> Go Back</a>
</div>
	<form method = "post" action = "{{URL::route('subject-teacher-create-post')}}" id = "backendForm">
		<div class="row">
			  <div class="col-sm-3">
			    <div class="form-group">
			      <label>Select Session</label>
			      {{HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'session_id', 
			      	$selected = 
			      		Input::has('session_id') ?
			      		Input::get('session_id') : AcademicSession::where('is_current','yes')->first()['id'])}}
			    </div>
			  </div>

			  <div class="col-sm-3">
			    <div class="form-group">
			      <label>Select class</label>
			      <select name="class_id" id="class_id" class="form-control">
							<option value="0">--Select Session First--</option>
						</select>
			    </div>
			  </div>

			  <div class="col-sm-3">
			    <div class="form-group">
			      <label>Select section</label>
			      <select name="section_id" id="section_id" class="form-control">
							<option value="0">--Select Class First--</option>
						</select>
			    </div>
			  </div>
		</div>
		<div class = "content" id = "subject-teacher-list">

		</div>
	</form>
</div>

<input type = "hidden" id = "ajax-get-class-ids-from-session-id" value = "{{URL::route('ajax-get-class-ids-from-session-id')}}">
<input type = "hidden" id = "ajax-get-section-ids-from-class-id" value = "{{URL::route('ajax-get-section-ids-from-class-id')}}">
<input type = "hidden" id = "ajax-get-subject-list-and-teacher-list-from-session-id-class-id-section-id" value = "{{URL::route('ajax-get-subject-list-and-teacher-list-from-session-id-class-id-section-id')}}">

@stop

@section('custom-js')
<script src = "{{ asset('backend-js/submit-enable-disable.js') }}"></script>
<script>
	/**
	 * Update the table showing subjects corresponding to the selected class and section
	**/

	//update class_id
	//update section_id

	$(function()
	{
		updateClassList();
		updateSectionList();

		$('#session_id').change(function(e)
		{
			updateClassList();
			updateSectionList();
			ajaxRequest();
		});

		$('#class_id').change(function(e)
		{
			updateSectionList();
			ajaxRequest();
		});

		$('#section_id').change(function(e)
		{
			ajaxRequest();
		});
	});

	function updateClassList()
	{
		//get academic_sesison_id
		var session_id = $('#session_id').val();

		var url = $('#ajax-get-class-ids-from-session-id').val();
         $('#class_id').html('<option value="0">loading...</option>');
         $.ajax( {
                      "url": url,
                      //"contentType": "application/json",
                      "data": {"session_id" : $('#session_id').val()},
                      "method": "GET",
                      //"dataType": "json"
                      } ).done(function(data) 
                      {
                        $('#class_id').html(data);
                      });

	}

	function updateSectionList()
	{
		//get academic_sesison_id
		var class_id = $('#class_id').val();

		var url = $('#ajax-get-section-ids-from-class-id').val();
        
         $('#section_id').html('<option value="0">loading...</option>');   
         $.ajax( {
                      "url": url,
                      //"contentType": "application/json",
                      "data": {"class_id" : $('#class_id').val()},
                      "method": "GET",
                      //"dataType": "json"
                      } ).done(function(data) 
                      {
                        $('#section_id').html(data);
                      });

	}

	function ajaxRequest()
	{
		var url = $('#ajax-get-subject-list-and-teacher-list-from-session-id-class-id-section-id').val();

			$.ajax( {
                      "url": url,
                      //"contentType": "application/json",
                      "data": {"session_id" : $('#session_id').val(), "class_id" : $('#class_id').val(), "section_id" : $('#section_id').val()},
                      "method": "GET",
                      //"dataType": "json"
                      } ).done(function(data) 
                      {
                        $('#subject-teacher-list').html(data);
                      });

	}

</script>

<script src="{{ asset('sms/plugins/iCheck/icheck.min.js')}}" type="text/javascript"></script>

@stop
