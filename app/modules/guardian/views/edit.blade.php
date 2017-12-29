@extends('backend.'.$current_user->role.'.main')
@section('custom-css')
	<!-- Theme style -->    
    <link href="{{asset('sms/plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('page-header')
	<h1>Edit Guardian Details</h1>
@stop

@section('content')

<div style="margin-bottom: 10px; display: block; clear: both; overflow: hidden">
  <a  href="#" onclick="history.go(-1);" class="btn btn-danger pull-right btn-flat"><i class="fa fa-fw fa-arrow-left"></i> Go Back</a>
</div>

	<div class = 'content'>

		<form method = "post" action = "{{URL::route($module_name.'-edit-post', array($id))}}"  id = "backendForm" enctype = "multipart/form-data">
		
			<div class = 'form-group @if($errors->has("guardian_name")) {{"has-error"}} @endif'>
				<label for = 'guardian_name'  class = 'control-label'>Guardian Name:</label>
					
				<input type = 'text' name = 'guardian_name' value= '{{$data['data']->guardian_name}}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('guardian_name')) {{$errors->first('guardian_name')}} @endif</span>
					
			</div>

			<!-- <div class = 'form-group @if($errors->has("dob_in_ad")) {{"has-error"}} @endif'>
				<label for = 'dob_in_ad'  class = 'control-label'>Guardian DOB(AD): <small>optional</small></label>
					
				<input type = 'text' name = 'dob_in_ad' value= '{{$data['data']->dob_in_ad}}' class = 'form-control'>
				<span class = 'help-block'>@if($errors->has('dob_in_ad')) {{$errors->first('dob_in_ad')}} @endif</span>
					
			</div> -->

			<div class = 'form-group @if($errors->has("current_address")) {{"has-error"}} @endif'>
				<label for = 'current_address'  class = 'control-label'>Current Address:</label>
					
				<input type = 'text' name = 'current_address' value= '{{ $data['data']->current_address }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('current_address')) {{$errors->first('current_address')}} @endif</span>
					
			</div>

			<div class = 'form-group @if($errors->has("permanent_address")) {{"has-error"}} @endif'>
				<label for = 'permanent_address'  class = 'control-label'>Permanent Address:</label>
					
				<input type = 'text' name = 'permanent_address' value= '{{ $data['data']->permanent_address }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('permanent_address')) {{$errors->first('permanent_address')}} @endif</span>
					
			</div>

			<div class = 'form-group @if($errors->has("primary_contact")) {{"has-error"}} @endif'>
				<label for = 'primary_contact'  class = 'control-label'>Primary Contact:</label>
					
				<input type = 'text' name = 'primary_contact' value= '{{ $data['data']->primary_contact }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('primary_contact')) {{$errors->first('primary_contact')}} @endif</span>
					
			</div>

			<div class = 'form-group @if($errors->has("secondary_contact")) {{"has-error"}} @endif'>
				<label for = 'secondary_contact'  class = 'control-label'>Secondary Contact:</label>
					
				<input type = 'text' name = 'secondary_contact' value= '{{ $data['data']->secondary_contact }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('secondary_contact')) {{$errors->first('secondary_contact')}} @endif</span>
					
			</div>

			<div class = 'form-group @if($errors->has("email")) {{"has-error"}} @endif'>
				<label for = 'email'  class = 'control-label'>Email:</label>
				<input type = 'text' name = 'email' value= '{{ $data['data']->email }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('email')) {{$errors->first('email')}} @endif</span>
			</div>


			<div class = 'form-group @if($errors->has("photo")) {{"has-error"}} @endif'>
				<label for = 'photo'  class = 'control-label'>Photo:</label>
				<br/>
				@if(strlen(trim($data['data']->photo)))
					<img src = "{{Config::get('app.url').'app/modules/guardian/assets/images/'.$data['data']->photo}}" height="auto" width="250">
				@else
					<img class="img-responsive"  class="img-responsive" src = "{{Config::get('app.url').'app/modules/guardian/assets/images/no-img.png'}}" >
				@endif
				<br/>
				<input type = 'file' name = 'photo'>
				<input type = "hidden" name = "original_photo" value = "{{$data['data']->photo}}">
				<span class = 'help-block'>@if($errors->has('photo')) {{$errors->first('photo')}} @endif</span>	
			</div>

			<div class = 'form-group @if($errors->has("occupation")) {{"has-error"}} @endif'>
				<label for = 'occupation'  class = 'control-label'>Occupation:</label>
				<input type = 'text' name = 'occupation' value= '{{ $data['data']->occupation }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('occupation')) {{$errors->first('occupation')}} @endif</span>
			</div>

			<div class = 'form-group @if($errors->has("is_active")) {{"has-error"}} @endif'>
				<label for = 'is_active'  class = 'control-label'>Is Active:</label>&nbsp;&nbsp;
				<input type = 'radio' name = 'is_active' value= 'yes' @if($data['data']->is_active == 'yes') checked @endif>&nbsp;&nbsp;Yes&nbsp;&nbsp;&nbsp;<input type = 'radio' name = 'is_active' value= 'no' @if($data['data']->is_active == 'no') checked @endif>&nbsp;&nbsp;No
				<span class = 'help-block'>@if($errors->has('is_active')) {{$errors->first('is_active')}} @endif</span>
			</div>

			<div class="form-group multi-field-wrapper">
	      <div class="multi-fields" id="wrapper">
	      	{{-- In the case of no related student, give a dummy --}}
	      	@if (count($data['related_students']) == 0)
	      		@define $data['related_students'][0] = new StdClass
	      		@define $data['related_students'][0]->username = ''
	      	@endif
	      	@foreach($data['related_students'] as $student)
	        <div class="multi-field">
	        	<label>Student Username: </label>
	          <input type="text" name="student_username[]" value="{{ $student->username }}">
	          <label>Relationship: </label>
	          <input type="text" name="relationship[]" value="{{ $student->relationship }}">
	          <button type="button" class="remove-field" onclick="removeStudent(this)">Remove</button>
	        </div>
	        @endforeach
	      </div>

	      <div class="form-group">
		    <a class="btn btn-info btn-flat" data-toggle="modal" data-target="#find-id" >
		    	<i class="fa fa-search"></i> Find ID
		    </a>
  			@include('include.modal-find-student')
		  </div>

			<input type = "hidden" name = "role" value = 'guardian'>
			<input type = "hidden" name = "id" value = "{{$data['data']->id}}">
			{{Form::token()}}
			<div class = "form-group">
				<input type = "submit" class = "btn btn-success btn-lg btn-flat" value = "Update">
			</div>
			
		</form>
	</div>

@stop

@section('custom-js')

<script src = "{{Config::get('app.url').'app/modules/guardian/assets/js/dynamicAddStudents.js'}}"></script>
<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "text/javascript"></script>
<script src="{{ asset('sms/plugins/iCheck/icheck.min.js')}}" type="text/javascript"></script>
<script>
$(document).ready(function(){
  $('input').iCheck({
    checkboxClass: 'icheckbox_minimal-blue',
    radioClass: 'iradio_minimal-blue',
    increaseArea: '20%' // optional
  });
});
</script>
@stop
