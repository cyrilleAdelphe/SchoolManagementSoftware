@extends('frontend.main')

@section('content')
	<form method = "post" action = "{{URL::route('students-register-post')}}">
		<div class="form-group">
			<label for="student_name">Full Name</label> 
			<input name = "student_name" id="student_name" class="form-control" type="text" placeholder="Enter full name" 
				value= "{{ (Input::old('student_name')) ? (Input::old('student_name')) : '' }}">
			<span class = "form-error">
				@if($errors->has('student_name')) 
					{{ $errors->first('student_name') }} 
				@endif
			</span>
		</div>

		<div class="form-group">
			<label for="email">Email ID</label> 
			<input name = "email" id="email" class="form-control" type="text" placeholder="Enter email" 
				value= "{{ (Input::old('email')) ? (Input::old('email')) : '' }}">
			<span class = "form-error">
				@if($errors->has('email')) 
					{{ $errors->first('email') }} 
				@endif
			</span>
		</div>

		<!--
		<div class="form-group">
			<label for="password">Password</label> 
			<input name = "password" id="password" class="form-control" type="text" placeholder="Enter password" 
				value= "{{ (Input::old('password')) ? (Input::old('password')) : '' }}">
			<span class = "form-error">
				@if($errors->has('password')) 
					{{ $errors->first('password') }} 
				@endif
			</span>
		</div>
		-->

		<div class="form-group">
			<label for="current_address">Current Address</label> 
			<input name = "current_address" id="current_address" class="form-control" type="text" placeholder="Enter current address" 
				value= "{{ (Input::old('current_address')) ? (Input::old('current_address')) : '' }}">
			<span class = "form-error">
				@if($errors->has('current_address')) 
					{{ $errors->first('current_address') }} 
				@endif
			</span>
		</div>

		<div class="form-group">
			<label for="permanent_address">Permanent Address</label> 
			<input name = "permanent_address" id="permanent_address" class="form-control" type="text" placeholder="Enter permanent address" 
				value= "{{ (Input::old('permanent_address')) ? (Input::old('permanent_address')) : '' }}">
			<span class = "form-error">
				@if($errors->has('permanent_address')) 
					{{ $errors->first('permanent_address') }} 
				@endif
			</span>
		</div>

		<div class="form-group">
			<label for="phone_no">Phone number</label> 
			<input name = "phone_no" id="phone_no" class="form-control" type="text" placeholder="Enter phone number" 
				value= "{{ (Input::old('phone_no')) ? (Input::old('phone_no')) : '' }}">
			<span class = "form-error">
				@if($errors->has('phone_no')) 
					{{ $errors->first('phone_no') }} 
				@endif
			</span>
		</div>

		<div class="form-group">
			<label for="sex">Gender</label> 
			<input name = "sex" id="sex" class="form-control" type="text" placeholder="Enter gender" 
				value= "{{ (Input::old('sex')) ? (Input::old('sex')) : '' }}">
			<span class = "form-error">
				@if($errors->has('sex')) 
					{{ $errors->first('sex') }} 
				@endif
			</span>
		</div>	

		<div class="form-group">
			<label for="dob_in_bs">DOB in BS</label> 
			<input name = "dob_in_bs" id="dob_in_bs" class="form-control" type="text" placeholder="Enter DOB in BS" 
				value= "{{ (Input::old('dob_in_bs')) ? (Input::old('dob_in_bs')) : '' }}">
			<span class = "form-error">
				@if($errors->has('dob_in_bs')) 
					{{ $errors->first('dob_in_bs') }} 
				@endif
			</span>
		</div>	

		<div class="form-group">
			<label for="dob_in_ad">DOB in AD</label> 
			<input name = "dob_in_ad" id="dob_in_ad" class="form-control" type="text" placeholder="Enter DOB in AD" 
				value= "{{ (Input::old('dob_in_ad')) ? (Input::old('dob_in_ad')) : '' }}">
			<span class = "form-error">
				@if($errors->has('dob_in_ad')) 
					{{ $errors->first('dob_in_ad') }} 
				@endif
			</span>
		</div>	

		<div class="form-group">
			<label for="guardian_contact">Guardian Contact</label> 
			<input name = "guardian_contact" id="guardian_contact" class="form-control" type="text" placeholder="Enter Guardian Contact" 
				value= "{{ (Input::old('guardian_contact')) ? (Input::old('guardian_contact')) : '' }}">
			<span class = "form-error">
				@if($errors->has('guardian_contact')) 
					{{ $errors->first('guardian_contact') }} 
				@endif
			</span>
		</div>	

		<div class="form-group">
			<label for="secondary_contact">Secondary Contact</label> 
			<input name = "secondary_contact" id="secondary_contact" class="form-control" type="text" placeholder="Enter Secondary Contact" 
				value= "{{ (Input::old('secondary_contact')) ? (Input::old('secondary_contact')) : '' }}">
			<span class = "form-error">
				@if($errors->has('secondary_contact')) 
					{{ $errors->first('secondary_contact') }} 
				@endif
			</span>
		</div>	

		<div class="form-group">
			<label for="registered_session_id">Registered Session ID</label> 
			<input name = "registered_session_id" id="registered_session_id" class="form-control" type="text" placeholder="Enter id" 
				value= "{{ (Input::old('registered_session_id')) ? (Input::old('registered_session_id')) : '' }}">
			<span class = "form-error">
				@if($errors->has('registered_session_id')) 
					{{ $errors->first('registered_session_id') }} 
				@endif
			</span>
		</div>	

		<div class="form-group">
			<label for="registered_class_id">Registered Class ID</label> 
			<input name = "registered_class_id" id="registered_class_id" class="form-control" type="text" placeholder="Enter id" 
				value= "{{ (Input::old('registered_class_id')) ? (Input::old('registered_class_id')) : '' }}">
			<span class = "form-error">
				@if($errors->has('registered_class_id')) 
					{{ $errors->first('registered_class_id') }} 
				@endif
			</span>
		</div>	

		<div class="form-group">
			<label for="registered_section_id">Registered Session ID</label> 
			<input name = "registered_section_id" id="registered_section_id" class="form-control" type="text" placeholder="Enter id" 
				value= "{{ (Input::old('registered_section_id')) ? (Input::old('registered_section_id')) : '' }}">
			<span class = "form-error">
				@if($errors->has('registered_section_id')) 
					{{ $errors->first('registered_section_id') }} 
				@endif
			</span>
		</div>	

		<div class="form-group">
			<label for="file">File</label> 
			<input name = "file" id="file" class="form-control" type="text" placeholder="Enter file" 
				value= "{{ (Input::old('file')) ? (Input::old('file')) : '' }}">
			<span class = "form-error">
				@if($errors->has('file')) 
					{{ $errors->first('file') }} 
				@endif
			</span>
		</div>	

		<div class="form-group">
			<label for="unique_school_roll_number">Roll No.</label> 
			<input name = "unique_school_roll_number" id="unique_school_roll_number" class="form-control" type="text" placeholder="Enter roll no." 
				value= "{{ (Input::old('unique_school_roll_number')) ? (Input::old('unique_school_roll_number')) : '' }}">
			<span class = "form-error">
				@if($errors->has('unique_school_roll_number')) 
					{{ $errors->first('unique_school_roll_number') }} 
				@endif
			</span>
		</div>	

		<input name = 'is_active' type = "hidden" value="yes"/>
		
		<div class="form-row">
			<button class="btn btn-primary" type="submit">Register</button>
		</div>
		{{ Form::token() }}
	</form>
@stop