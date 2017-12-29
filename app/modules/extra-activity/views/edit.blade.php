@extends('backend.' . $role . '.main')

@section('content')
<form method = "post" action = "{{URL::route($module_name . '-edit-post', $data ? $data->event_id : 0)}}">	
	<div class="form-group {{($errors->has('event_code')) ? 'has-error' : ''}}">
    <label for="title">Event's code</label>
    <input name = "event_code" id="title" class="form-control" type="text" placeholder="Enter event's code" value = "{{ Input::old('event_code') ? Input::old('event_code') : $data->event_code }}">
  </div>
  <label>Students to highlight</label>

  <div class="form-group multi-field-wrapper">
  	<div class="multi-fields" id="wrapper">
  		@foreach ($data->student_list as $student)
	  		<div class="multi-field row">
	  			<div class="col-sm-5">
			      <div class="form-group">
			        <input id="title" name = "student_username[]" class="form-control" type="text" placeholder="Enter student's code" value = "{{ $student->username }}">
			      </div>
			    </div>
			    
			    <div class="col-sm-5">
			      <div class="form-group">
			        <input id="title" name = "remarks[]" class="form-control" type="text" placeholder="Enter remarks or position" value = "{{ $student->remarks }}">
			      </div>
			    </div>
				  
				  <button type="button" class="remove-field" onclick="removeStudent(this)">Remove</button>
				</div>
			@endforeach
		</div>
		<a class="btn btn-info btn-sm" data-toggle="modal" data-target="#find-id" ><i class="fa fa-search"></i> Find ID</a>
  	@include('include.modal-find-student')
	</div>
	{{Form::token()}}
	<input type = "hidden" name = "is_active" value = "yes" />
	<input type = "hidden" name = "old_event_id" value = "{{ $data->event_id }}" />
	<div class="form-group">
    <button class="btn btn-primary" type="submit">Submit</button>
  </div>
</form>

@stop

@section('custom-js')
	<script src = "{{Config::get('app.url').'app/modules/extra-activity/assets/js/dynamicAddStudents.js'}}"></script>
@stop