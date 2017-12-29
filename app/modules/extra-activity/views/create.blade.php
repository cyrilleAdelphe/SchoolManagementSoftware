@extends('include.form-tabs')

@section('tab-content')
<form method = "post" action = "{{URL::route($module_name . '-create-post')}}">	
	<div class="form-group {{($errors->has('event_code')) ? 'has-error' : ''}}">
    <label for="title">Event's code</label>
    <input name = "event_code" id="title" class="form-control" type="text" placeholder="Enter event's code" value = "{{ Input::old('event_code') ? Input::old('event_code') : '' }}">
  </div>
  <label>Students to highlight</label>

  <div class="form-group multi-field-wrapper">
  	<div class="multi-fields" id="wrapper">
  		
		</div>
		<a class="btn btn-info btn-sm" data-toggle="modal" data-target="#find-id" ><i class="fa fa-search"></i> Find ID</a>
  	@include('include.modal-find-student')
	</div>
	{{Form::token()}}
	<input type = "hidden" name = "is_active" value = "yes" />
	<div class="form-group">
    <button class="btn btn-primary" type="submit">Submit</button>
  </div>
</form>

@stop

@section('custom-js')

<script src = "{{ asset('backend-js/dynamicAddFields.js') }}" type = "text/javascript"></script>
<script src = "{{Config::get('app.url').'app/modules/extra-activity/assets/js/dynamicAddStudents.js'}}"></script>
@stop