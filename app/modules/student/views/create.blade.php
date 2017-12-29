@extends('student.views.form-tabs')
@section('custom-css')
 <link href="{{asset('sms/plugins/daterangepicker/daterangepicker-bs3-event.css')}}" rel="stylesheet" type="text/css" />
 <link href="{{asset('sms/assets/css/nepali.datepicker.v2.2.min.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('tab-content')

<div class="tab-pane " id="tab_2">

	{{-- $actionButtons --}}

		<form method = "post" action = "{{URL::route($module_name.'-create-post')}}"   id = "backendForm" enctype = "multipart/form-data">
			<div class="row">
				<div class="col-sm-2">
					<div class = 'form-group @if($errors->has("registered_session_id")) {{"has-error"}} @endif'>
						<label for = 'name'  class = 'control-label'>Session:</label>
						{{HelperController::generateSelectList('AcademicSession', 
																										'session_name', 
																										'id', 
																										'registered_session_id', 
																										Input::old('registered_session_id') ? Input::old('registered_session_id') : HelperController::getCurrentSession()
																									)}}
						<span class = 'help-block'>@if($errors->has('registered_session_id')) {{$errors->first('registered_session_id')}} @endif</span>
					</div>
				</div>
				<div class="col-sm-4">
					<div class = 'form-group @if($errors->has("student_name")) {{"has-error"}} @endif'>
						<label for = 'student_name'  class = 'control-label'>First Name :</label>						
						<input type = 'text' name = 'student_name' value= '{{ (Input::old('student_name')) ? (Input::old('student_name')) : '' }}' class = 'form-control required'>
						<span class = 'help-block'>@if($errors->has('student_name')) {{$errors->first('student_name')}} @endif</span>							
					</div>
				</div>
				<div class="col-sm-3">
					<div class = 'form-group @if($errors->has("last_name")) {{"has-error"}} @endif'>
						<label for = 'last_name'  class = 'control-label'>Last Name :</label>							
						<input type = 'text' name = 'last_name' value= '{{ (Input::old('last_name')) ? (Input::old('last_name')) : '' }}' class = 'form-control required'>
						<span class = 'help-block'>@if($errors->has('last_name')) {{$errors->first('last_name')}} @endif</span>							
					</div>
				</div>
				<div class="col-sm-3">
					<div class = 'form-group @if($errors->has("sex")) {{"has-error"}} @endif'>
						<label for = 'sex'  class = 'control-label'>Gender :</label>
							
						{{HelperController::generateStaticSelectList(array('male' => 'Male', 'female' => 'Female', 'other' => 'Other'), 'sex', Input::old('sex'))}}
						<span class = 'help-block'>@if($errors->has('sex')) {{$errors->first('sex')}} @endif</span>
							
					</div>
				</div>
				
			</div>

			<div class="row">
				<div class="col-sm-3">
					<div class = 'form-group @if($errors->has("current_roll_number")) {{"has-error"}} @endif'>
						<label for = 'current_roll_number'  class = 'control-label'>Roll Number :</label>
							
						<input type = 'text' name = 'current_roll_number' value= '{{ (Input::old('current_roll_number')) ? (Input::old('current_roll_number')) : '' }}' class = 'form-control required'>
						<span class = 'help-block'>@if($errors->has('current_roll_number')) {{$errors->first('current_roll_number')}} @endif</span>
							
					</div>
				</div>
				
				<div class="col-sm-3">
					<div id = "div_for_registered_class_id">				
						<div class = 'form-group @if($errors->has("registered_class_id")) {{"has-error"}} @endif'>
							<label for = 'registered_class_id'  class = 'control-label'>Class:</label>
								<select id = "registered_class_id" name = "registered_class_id" class = "form-control">
									<option>Please select session first</option>
								</select>
							<span class = 'help-block'>@if($errors->has('registered_class_id')) {{$errors->first('registered_class_id')}} @endif</span>
						</div>
						
					</div>
				</div>
				<div class="col-sm-3">
					<div id = "div_for_registered_section_code">				
							<div class = 'form-group @if($errors->has("registered_section_code")) {{"has-error"}} @endif'>
								<label for = 'registered_section_code'  class = 'control-label'>Section:</label>
									<select id = "registered_section_code" name = "registered_section_code" class = "form-control">
										<option>Please select class first</option>
									</select>
								<span class = 'help-block'>@if($errors->has('registered_section_code')) {{$errors->first('registered_section_code')}} @endif</span>
							</div>						
					</div>		
				</div>
				<div class="col-sm-3">
					<div class = 'form-group '>
						<label for = 'house_id'  class = 'control-label'>House:</label>
							<select name = "house_id" class = "form-control">
							<option>--Select House--</option>
								@foreach($house as $key=>$value)
									<option value="{{$key}}">{{$value}}</option>
								@endforeach
							</select>
						<span class = 'help-block'></span>
					</div>
				</div>
			</div>

			
			<div class="row">
				<div class="col-sm-4">
					<div class = 'form-group '>
						<label for = 'ethnicity_id'  class = 'control-label'>Ethnicity:</label>
						<select name = "ethnicity_id" class = "form-control">
							<option>--Select Ethnicity--</option>
							@foreach($ethnicity as $key=>$value)
								<option value="{{$key}}">{{$value}}</option>
							@endforeach
						</select>
						<span class = 'help-block'></span>
					</div>
				</div>
				<div class="col-sm-4">
					<div class = 'form-group '>
						<label for = 'dob_in_ad'  class = 'control-label'>DOB (B.S.) </label>
						<div class="input-group">
		                 	<div class="input-group-addon"><i class="fa fa-calendar"></i></div>	
							<input class="form-control" type="text" id="nepaliDate9" value="YYYY-MM-DD"/>
						</div>					
					</div>
				</div>
				<div class="col-sm-4">
					<div class = 'form-group @if($errors->has("dob_in_ad")) {{"has-error"}} @endif'>
						<label for = 'dob_in_ad'  class = 'control-label'>DOB(A.D.) :</label>
						<div class="input-group">
							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>	
							<input class="form-control"  type="text" id="englishDate9" name = 'dob_in_ad' value="YYYY-MM-DD"  />
							<span class = 'help-block'>@if($errors->has('dob_in_ad')) {{$errors->first('dob_in_ad')}} @endif</span>
						</div>
					</div>
				</div>
			</div>
			

			<div class="row">
				<div class="col-sm-6">
					<div class = 'form-group @if($errors->has("current_address")) {{"has-error"}} @endif'>
						<label for = 'current_address'  class = 'control-label'>Current Address :</label>
							
						<input type = 'text' name = 'current_address' value= '{{ (Input::old('current_address')) ? (Input::old('current_address')) : '' }}' class = 'form-control required'>
						<span class = 'help-block'>@if($errors->has('current_address')) {{$errors->first('current_address')}} @endif</span>
							
					</div>
				</div>
				<div class="col-sm-6">
					<div class = 'form-group @if($errors->has("permanent_address")) {{"has-error"}} @endif'>
						<label for = 'permanent_address'  class = 'control-label'>Permanent Address :</label>
							
						<input type = 'text' name = 'permanent_address' value= '{{ (Input::old('permanent_address')) ? (Input::old('permanent_address')) : '' }}' class = 'form-control required'>
						<span class = 'help-block'>@if($errors->has('permanent_address')) {{$errors->first('permanent_address')}} @endif</span>
							
					</div>
				</div>
				
			</div>

			<div class="row">
				<div class="col-sm-4">
					<div class = 'form-group @if($errors->has("guardian_contact")) {{"has-error"}} @endif'>
						<label for = 'guardian_contact'  class = 'control-label'>Guardian Contact :</label>
							
						<input type = 'text' name = 'guardian_contact' value= '{{ (Input::old('guardian_contact')) ? (Input::old('guardian_contact')) : '' }}' class = 'form-control required'>
						<span class = 'help-block'>@if($errors->has('guardian_contact')) {{$errors->first('guardian_contact')}} @endif</span>
							
					</div>
				</div>
				<div class="col-sm-4">
					<div class = 'form-group @if($errors->has("secondary_contact")) {{"has-error"}} @endif'>
						<label for = 'secondary_contact'  class = 'control-label'>Secondary Contact :</label>
							
						<input type = 'text' name = 'secondary_contact' value= '{{ (Input::old('secondary_contact')) ? (Input::old('secondary_contact')) : '' }}' class = 'form-control required'>
						<span class = 'help-block'>@if($errors->has('secondary_contact')) {{$errors->first('secondary_contact')}} @endif</span>
							
					</div>
				</div>
				<div class="col-sm-4">
					<div class = 'form-group @if($errors->has("email")) {{"has-error"}} @endif'>
						<label for = 'email'  class = 'control-label'>Email :</label>
							
						<input type = 'text' name = 'email' value= '{{ (Input::old('email')) ? (Input::old('email')) : '' }}' class = 'form-control required'>
						<span class = 'help-block'>@if($errors->has('email')) {{$errors->first('email')}} @endif</span>
							
					</div>
				</div>
			</div>

			

		
			

			<div class = 'form-group @if($errors->has("photo")) {{"has-error"}} @endif'>
				<label for = 'photo'  class = 'control-label'>Photo :</label>					
				<input type = 'file' name = 'photo' value= '{{ (Input::old('photo')) ? (Input::old('photo')) : '' }}'>
				<span class = 'help-block'>@if($errors->has('photo')) {{$errors->first('photo')}} @endif</span>
					
			</div>


			{{-- <div class = 'form-group @if($errors->has("username")) {{"has-error"}} @endif'>
				<label for = 'username'  class = 'control-label'>Username :</label>
					
						<input type = 'text' name = 'username' value= '{{ (Input::old('username')) ? (Input::old('username')) : '' }}' class = 'form-control required'>
						<span class = 'help-block'>@if($errors->has('username')) {{$errors->first('username')}} @endif</span>
					
			</div>

			<div class = 'form-group @if($errors->has("password")) {{"has-error"}} @endif'>
				<label for = 'password'  class = 'control-label'>Password :</label>
					
						<input type = 'password' name = 'password' value= '{{ (Input::old('password')) ? (Input::old('password')) : '' }}' class = 'form-control required'>
						<span class = 'help-block'>@if($errors->has('password')) {{$errors->first('password')}} @endif</span>
					
			</div>

			<div class = 'form-group @if($errors->has("confirm_password")) {{"has-error"}} @endif'>
				<label for = 'confirm_password'  class = 'control-label'>Confirm Password :</label>
					
						<input type = 'password' name = 'confirm_password' value= '{{ (Input::old('confirm_password')) ? (Input::old('confirm_password')) : '' }}' class = 'form-control required'>
						<span class = 'help-block'>@if($errors->has('confirm_password')) {{$errors->first('confirm_password')}} @endif</span>
					
			</div> --}}

			<input type = 'hidden' name = 'password' value= 'password'>
			<input type = 'hidden' name = 'confirm_password' value= 'password'>
			
			<input type = "hidden" name = "role" value = "student">


			<input type = 'hidden' name = 'is_active' value = 'yes'>
			{{Form::token()}}
			<div class = "form-group">
				<input type = "submit" class = "btn btn-success btn-lg btn-flat" value = "Create">
			</div>
			
		</form>

</div>
@stop

@section('custom-js')
<script src = "{{ asset('backend-js/validation.js') }}" type = "text/javascript"></script>
	<script type = "text/javascript">

		function updateClass()
		{
			var registered_session_id = $('#registered_session_id').val();
			if(registered_session_id == 0)
			{
				return;
			}

			$.ajax({
					            "url": "{{URL::route('student-ajax-active-classes')}}",
					            "data": {"session_id" : registered_session_id},
					            "method": "GET"
		          			}).done(function(data) {
					 				
					 				$('#registered_class_id').html(data);
					});
		}
		$(function()
		{
			var ajax_url = $('#ajax_url').val();
			
			updateClass();

			$('#registered_session_id').change(function()
			{
				updateClass();
			});
		
			$('#registered_class_id').change(function()
			{

				var registered_class_id = $(this).val();
			
				$.ajax( {
					            "url": "{{URL::route('student-ajax-active-sections')}}",
					            "data": {"class_id" : registered_class_id},
					            "method": "GET"
			          			} ).done(function(data) {
									$('#registered_section_code').html(data);
								});		
			});
		});
	</script>

<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "text/javascript"></script>


<script src = "{{ asset('sms/assets/js/nepali.datepicker.v2.2.min.js') }}" type = "text/javascript"></script>
<script>
     $(document).ready(function(){
        $('#englishDate9').change(function(){
			$('#nepaliDate9').val(AD2BS($('#englishDate9').val()));
		});

		$('#nepaliDate9').change(function(){
			$('#englishDate9').val(BS2AD($('#nepaliDate9').val()));
		});
	});
</script>

@stop
