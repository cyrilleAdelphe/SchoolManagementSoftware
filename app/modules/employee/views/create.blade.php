@extends('employee.views.form-tabs')
@section('custom-css')
 <link href="{{asset('sms/plugins/daterangepicker/daterangepicker-bs3-event.css')}}" rel="stylesheet" type="text/css" />
 <link href="{{asset('sms/plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css" />
@stop
@section('tab-content')


<div class = 'content'>
	<form method = "post" action = "{{URL::route($module_name.'-create-post')}}"  class = "form-horizontal" id = "backendForm" enctype = "multipart/form-data">
	
		<div class = 'form-group @if($errors->has("employee_name")) {{"has-error"}} @endif'>
			<label for = 'employee_name'  class = 'control-label'>Employee Name :</label>
				
			<input type = 'text' name = 'employee_name' value= '{{ (Input::old('employee_name')) ? (Input::old('employee_name')) : '' }}' class = 'form-control required'>
			<span class = 'help-block'>@if($errors->has('employee_name')) {{$errors->first('employee_name')}} @endif</span>
				
		</div>

		<div class = 'form-group @if($errors->has("employee_dob_in_ad")) {{"has-error"}} @endif'>
			<label for = 'employee_dob_in_ad'  class = 'control-label'>Employee DOB(AD) :</label>
				
			<input type = 'text' name = 'employee_dob_in_ad' value= '{{ (Input::old('employee_dob_in_ad')) ? (Input::old('employee_dob_in_ad')) : '' }}' class = 'form-control myDate'>
			<span class = 'help-block'>@if($errors->has('employee_dob_in_ad')) {{$errors->first('employee_dob_in_ad')}} @endif</span>
				
		</div>

		<div class = 'form-group @if($errors->has("sex")) {{"has-error"}} @endif'>
			<label for = 'sex'  class = 'control-label'>Sex :</label>
				
			{{HelperController::generateStaticSelectList(array('male' => 'Male', 'female' => 'Female', 'other' => 'Other'), 'sex', Input::old('sex'))}}
			<span class = 'help-block'>@if($errors->has('sex')) {{$errors->first('sex')}} @endif</span>
				
		</div>

		<div class = 'form-group @if($errors->has("current_address")) {{"has-error"}} @endif'>
			<label for = 'current_address'  class = 'control-label'>Current Address :</label>
				
			<input type = 'text' name = 'current_address' value= '{{ (Input::old('current_address')) ? (Input::old('current_address')) : '' }}' class = 'form-control'>
			<span class = 'help-block'>@if($errors->has('current_address')) {{$errors->first('current_address')}} @endif</span>
				
		</div>

		<div class = 'form-group @if($errors->has("permanent_address")) {{"has-error"}} @endif'>
			<label for = 'permanent_address'  class = 'control-label'>Permanent Address :</label>
				
			<input type = 'text' name = 'permanent_address' value= '{{ (Input::old('permanent_address')) ? (Input::old('permanent_address')) : '' }}' class = 'form-control'>
			<span class = 'help-block'>@if($errors->has('permanent_address')) {{$errors->first('permanent_address')}} @endif</span>
				
		</div>

		<div class = 'form-group @if($errors->has("primary_contact")) {{"has-error"}} @endif'>
			<label for = 'primary_contact'  class = 'control-label'>Primary Contact :</label>
				
			<input type = 'text' name = 'primary_contact' value= '{{ (Input::old('primary_contact')) ? (Input::old('primary_contact')) : '' }}' class = 'form-control'>
			<span class = 'help-block'>@if($errors->has('primary_contact')) {{$errors->first('primary_contact')}} @endif</span>
				
		</div>

		<div class = 'form-group @if($errors->has("secondary_contact")) {{"has-error"}} @endif'>
			<label for = 'secondary_contact'  class = 'control-label'>Secondary Contact :</label>
				
			<input type = 'text' name = 'secondary_contact' value= '{{ (Input::old('secondary_contact')) ? (Input::old('secondary_contact')) : '' }}' class = 'form-control'>
			<span class = 'help-block'>@if($errors->has('secondary_contact')) {{$errors->first('secondary_contact')}} @endif</span>
				
		</div>

		<div class = 'form-group @if($errors->has("email")) {{"has-error"}} @endif'>
			<label for = 'email'  class = 'control-label'>Email :</label>
				
			<input type = 'text' name = 'email' value= '{{ (Input::old('email')) ? (Input::old('email')) : '' }}' class = 'form-control'>
			<span class = 'help-block'>@if($errors->has('email')) {{$errors->first('email')}} @endif</span>
				
		</div>

		<div class = 'form-group @if($errors->has("joining_date_in_ad")) {{"has-error"}} @endif'>
			<label for = 'joining_date_in_ad'  class = 'control-label'>Joining Date In AD :</label>
				
			<input type = 'text' name = 'joining_date_in_ad' value= '{{ (Input::old('joining_date_in_ad')) ? (Input::old('joining_date_in_ad')) : '' }}' class = 'form-control myDate'>
			<span class = 'help-block'>@if($errors->has('joining_date_in_ad')) {{$errors->first('joining_date_in_ad')}} @endif</span>
				
		</div>

		{{-- <div class = 'form-group @if($errors->has("position")) {{"has-error"}} @endif'>
			<label for = 'position'  class = 'control-label'>Position :</label>
				
			<input type = 'text' name = 'position' value= '{{ (Input::old('position')) ? (Input::old('position')) : '' }}' class = 'form-control'>
			<span class = 'help-block'>@if($errors->has('position')) {{$errors->first('position')}} @endif</span>
				
		</div> --}}

		<div class = 'form-group @if($errors->has("photo")) {{"has-error"}} @endif'>
			<label for = 'photo'  class = 'control-label'>Photo :</label>
				
			<input type = 'file' name = 'photo' value= '{{ (Input::old('photo')) ? (Input::old('photo')) : '' }}'>
			<span class = 'help-block'>@if($errors->has('photo')) {{$errors->first('photo')}} @endif</span>
		</div>

		<div class = 'form-group @if($errors->has("cv")) {{"has-error"}} @endif'>
			<label for = 'cv'  class = 'control-label'>CV :</label>
				
			<input type = 'file' name = 'cv' value= '{{ (Input::old('cv')) ? (Input::old('cv')) : '' }}' >
			<span class = 'help-block'>@if($errors->has('cv')) {{$errors->first('cv')}} @endif</span>
		</div>

		<div class = 'form-group @if($errors->has("group_id")) {{"has-error"}} @endif'>
			<label for = 'password'  class = 'control-label'>Select Role :&nbsp;&nbsp;&nbsp;</label>
			<!-- make foreach statement here  -->
			<div class="row"> 
			@foreach($posts as $group_id => $p)
				<div class="col-sm-2"><input type = "checkbox" name = "group_id[]" value = "{{$group_id}}" class = "eton_group">&nbsp;&nbsp; {{$p}}</div>
			@endforeach
			</div>
		</div>


		<input type = "hidden" id = "eton_allow_backend_access_checkbox" name = "eton_allow_backend_access_checkbox" value="1" />
		{{-- <div class = 'form-group'>
			<label for = 'Allow Backend Access'  class = 'control-label'>Allow Backend Access :</label>
			<!-- make foreach statement here  -->
			<span> <input type = "checkbox" id = "eton_allow_backend_access_checkbox" name = "eton_allow_backend_access_checkbox" @if(Input::old('eton_allow_backend_access_checkbox')) checked @endif></span>
		</div> --}}

		{{-- <div id ="eton_allow_backend_access" @if(Input::old('eton_allow_backend_access_checkbox')) style = "display:block" @else style = "display:none" @endif> --}}
		{{-- <div id ="eton_allow_backend_access">
			<!-- -->
			<div class = 'form-group @if($errors->has("username")) {{"has-error"}} @endif'>
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
					
			</div>
			--}}
			<input type = 'hidden' name = 'password' value= 'password'>
			<input type = 'hidden' name = 'confirm_password' value= 'password'>

			<input type = "hidden" name = "is_working" value = 'yes'>
			<input type = "hidden" name = "leave_date_in_ad" value = ''>
			<input type = "hidden" name = "leave_date_in_bs" value = ''>

		</div>

		<input type = 'hidden' name = 'is_active' value = 'yes'>
		{{Form::token()}}
		<div class = "form-group">
			<input type = "submit" class = "btn btn-success btn-lg btn-flat submit-enable-disable" value = "Create" related-form='backendForm'>
		</div>
		
	</form>
</div>

@stop

@section('custom-js')

{{File::get(app_path().'/modules/employee/assets/js/allow_backend_access.js')}}

<script src = "{{ asset('backend-js/validation.js') }}" type = "text/javascript"></script> 
<script src = "{{ asset('backend-js/submit-enable-disable.js') }}" type = "text/javascript"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js" type="text/javascript"></script>
 <script src="{{asset('sms/plugins/daterangepicker/daterangepicker-event.js')}}" type="text/javascript"></script>

<script type="text/javascript">
		$(function() {

		    $('.myDate').daterangepicker({
		    	 autoUpdateInput: false,
		        singleDatePicker: true,
		        showDropdowns: true,	        

		    }, 
		    function(start, end, label) {
		        var years = moment().diff(start, 'years');
		    });
		});

		$('.myDate').on('apply.daterangepicker', function(ev, picker){

      		$(this).val(picker.startDate.format('YYYY-MM-DD'));

  		});

</script>

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
