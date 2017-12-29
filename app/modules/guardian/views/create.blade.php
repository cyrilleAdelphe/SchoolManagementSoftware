@extends('guardian.views.form-tabs')

@section('tab-content')

	{{--$actionButtons--}}

	<div class = 'content'>
		<form method = "post" action = "{{URL::route($module_name.'-create-post')}}" id = "backendForm" enctype = "multipart/form-data">
		
			<div class = 'form-group @if($errors->has("guardian_name")) {{"has-error"}} @endif'>
				<label for = 'guardian_name'  class = 'control-label'>Guardian Name :</label>
					
				<input type = 'text' name = 'guardian_name' value= '{{ (Input::old('guardian_name')) ? (Input::old('guardian_name')) : '' }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('guardian_name')) {{$errors->first('guardian_name')}} @endif</span>
					
			</div>

			<!-- <div class = 'form-group @if($errors->has("dob_in_ad")) {{"has-error"}} @endif'>
				<label for = 'dob_in_ad'  class = 'control-label'>Guardian DOB(AD): <small>optional</small></label>
					
				<input type = 'text' name = 'dob_in_ad' value= '{{ (Input::old('dob_in_ad')) ? (Input::old('dob_in_ad')) : '' }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('dob_in_ad')) {{$errors->first('dob_in_ad')}} @endif</span>
					
			</div> -->

			<div class = 'form-group @if($errors->has("current_address")) {{"has-error"}} @endif'>
				<label for = 'current_address'  class = 'control-label'>Current Address :</label>
					
				<input type = 'text' name = 'current_address' value= '{{ (Input::old('current_address')) ? (Input::old('current_address')) : '' }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('current_address')) {{$errors->first('current_address')}} @endif</span>
					
			</div>

			<div class = 'form-group @if($errors->has("permanent_address")) {{"has-error"}} @endif'>
				<label for = 'permanent_address'  class = 'control-label'>Permanent Address :</label>
					
				<input type = 'text' name = 'permanent_address' value= '{{ (Input::old('permanent_address')) ? (Input::old('permanent_address')) : '' }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('permanent_address')) {{$errors->first('permanent_address')}} @endif</span>
					
			</div>

			<div class = 'form-group @if($errors->has("primary_contact")) {{"has-error"}} @endif'>
				<label for = 'primary_contact'  class = 'control-label'>Primary Contact :</label>
					
				<input type = 'text' name = 'primary_contact' value= '{{ (Input::old('primary_contact')) ? (Input::old('primary_contact')) : '' }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('primary_contact')) {{$errors->first('primary_contact')}} @endif</span>
					
			</div>

			<div class = 'form-group @if($errors->has("secondary_contact")) {{"has-error"}} @endif'>
				<label for = 'secondary_contact'  class = 'control-label'>Secondary Contact :</label>
					
				<input type = 'text' name = 'secondary_contact' value= '{{ (Input::old('secondary_contact')) ? (Input::old('secondary_contact')) : '' }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('secondary_contact')) {{$errors->first('secondary_contact')}} @endif</span>
					
			</div>

			<div class = 'form-group @if($errors->has("email")) {{"has-error"}} @endif'>
				<label for = 'email'  class = 'control-label'>Email :</label>
				<input type = 'text' name = 'email' value= '{{ (Input::old('email')) ? (Input::old('email')) : '' }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('email')) {{$errors->first('email')}} @endif</span>
			</div>


			<div class = 'form-group @if($errors->has("photo")) {{"has-error"}} @endif'>
				<label for = 'photo'  class = 'control-label'>Photo :</label>
				<input type = 'file' name = 'photo' value= '{{ (Input::old('photo')) ? (Input::old('photo')) : '' }}' class = 'required'>
				<span class = 'help-block'>@if($errors->has('photo')) {{$errors->first('photo')}} @endif</span>	
			</div>

			<div class = 'form-group @if($errors->has("occupation")) {{"has-error"}} @endif'>
				<label for = 'occupation'  class = 'control-label'>Occupation :</label>
				<input type = 'text' name = 'occupation' value= '{{ (Input::old('occupation')) ? (Input::old('occupation')) : '' }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('occupation')) {{$errors->first('occupation')}} @endif</span>
			</div>

			<input type="hidden" id="eton_allow_backend_access_checkbox" name="eton_allow_backend_access_checkbox" value="1" />
			{{-- <div class = 'form-group'>
				<label for = 'Allow Backend Access'  class = 'control-label'>Allow Backend Access :</label>
				<!-- make foreach statement here  -->
				<span><input type = "checkbox" id = "eton_allow_backend_access_checkbox" name = "eton_allow_backend_access_checkbox" @if(Input::old('eton_allow_backend_access_checkbox')) checked @endif></span>
			</div> --}}

			{{-- <div id ="eton_allow_backend_access" @if(Input::old('eton_allow_backend_access_checkbox')) style = "display:block" @else style = "display:none" @endif> --}}
			<div id ="eton_allow_backend_access">
				<!-- -->
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

				<input type = "hidden" name = "role" value = 'guardian'>
			</div>

			<!-- do this is another tab -->
			{{-- <div id = "div_for_ajax_search_results">
				<div class = 'form-group @if($errors->has("relationship")) {{"has-error"}} @endif'>
					<label for = 'relationship'  class = 'control-label'>Relationship :</label>
					<input type = 'text' name = 'relationship' value= '{{ (Input::old('relationship')) ? (Input::old('relationship')) : '' }}' class = 'form-control required'>
					<span class = 'help-block'>@if($errors->has('relationship')) {{$errors->first('relationship')}} @endif</span>
				</div>
			</div> --}}


			<div class="form-group multi-field-wrapper">
				<label> Related Students: </label>
	      <div class="multi-fields" id="wrapper">
	        
	      </div>
		    <a class="btn btn-info btn-sm btn-flat" data-toggle="modal" data-target="#find-id" ><i class="fa fa-search"></i> Find ID</a>
  			@include('include.modal-find-student')
		  </div>

			<input type = 'hidden' name = 'is_active' value = 'yes'>
			<input type = "hidden" id = "ajax_url" value = "{{URL::route('ajax-search-students')}}">
			{{Form::token()}}
			<div class = "form-group">
				<input type = "submit" class = "btn btn-success btn-lg btn-flat submit-enable-disable" value = "Create" related-form='backendForm'>
			</div>
			
		</form>
	</div>


@stop

@section('custom-js')

{{File::get(app_path().'/modules/guardian/assets/js/create.js')}}
<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "text/javascript"></script>
<script src = "{{ asset('backend-js/submit-enable-disable.js') }}" type = "text/javascript"></script>
<script src = "{{Config::get('app.url').'app/modules/guardian/assets/js/dynamicAddStudents.js'}}"></script>
@stop
