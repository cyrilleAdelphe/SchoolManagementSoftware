@extends('backend.'.$current_user->role.'.main')
@section('custom-css')
 <link href="{{asset('sms/plugins/daterangepicker/daterangepicker-bs3-event.css')}}" rel="stylesheet" type="text/css" />
 <link href="{{asset('sms/plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css" />
@stop
@section('content')

 <div style="margin-bottom: 15px; display: block; clear: both; overflow: hidden;">
	<a  href="#" onclick="history.go(-1);" class="btn btn-danger btn-flat pull-right"><i class="fa fa-fw fa-arrow-left "></i> Go Back</a>
</div>           
	<div class="tab-pane active" id="tab_1">
	    @if($data['data'])
		    {{-- $actionButtons --}}

		    <form method = "post" action = "{{URL::route($module_name.'-edit-post', array($id))}}" id = "backendForm" enctype = "multipart/form-data">
		        <div class = 'form-group @if($errors->has("employee_name")) {{"has-error"}} @endif'>
				<label for = 'employee_name'  class = 'control-label'>Employee Name :</label>
					
				<input type = 'text' name = 'employee_name' value= '{{$data['data']->employee_name}}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('employee_name')) {{$errors->first('employee_name')}} @endif</span>
					
				</div>

				<div class = 'form-group @if($errors->has("employee_dob_in_ad")) {{"has-error"}} @endif'>
					<label for = 'employee_dob_in_ad'  class = 'control-label'>Employee DOB(AD) :</label>
						
					<input type = 'text' name = 'employee_dob_in_ad' value= '{{$data['data']->employee_dob_in_ad}}' class = 'form-control myDate'>
					<span class = 'help-block'>@if($errors->has('employee_dob_in_ad')) {{$errors->first('employee_dob_in_ad')}} @endif</span>
						
				</div>

				<div class = 'form-group @if($errors->has("sex")) {{"has-error"}} @endif'>
					<label for = 'sex'  class = 'control-label'>Sex :</label>
						
					{{HelperController::generateStaticSelectList(array('male' => 'Male', 'female' => 'Female', 'other' => 'Other'), 'sex', $data['data']->sex)}}
					<span class = 'help-block'>@if($errors->has('sex')) {{$errors->first('sex')}} @endif</span>
						
				</div>

				<div class = 'form-group @if($errors->has("current_address")) {{"has-error"}} @endif'>
					<label for = 'current_address'  class = 'control-label'>Current Address :</label>
						
					<input type = 'text' name = 'current_address' value= '{{$data['data']->current_address}}' class = 'form-control'>
					<span class = 'help-block'>@if($errors->has('current_address')) {{$errors->first('current_address')}} @endif</span>
						
				</div>

				<div class = 'form-group @if($errors->has("permanent_address")) {{"has-error"}} @endif'>
					<label for = 'permanent_address'  class = 'control-label'>Permanent Address :</label>
						
					<input type = 'text' name = 'permanent_address' value= '{{$data['data']->current_address}}' class = 'form-control'>
					<span class = 'help-block'>@if($errors->has('permanent_address')) {{$errors->first('permanent_address')}} @endif</span>
						
				</div>

				<div class = 'form-group @if($errors->has("primary_contact")) {{"has-error"}} @endif'>
					<label for = 'primary_contact'  class = 'control-label'>Primary Contact :</label>
						
					<input type = 'text' name = 'primary_contact' value= '{{$data['data']->primary_contact}}' class = 'form-control'>
					<span class = 'help-block'>@if($errors->has('primary_contact')) {{$errors->first('primary_contact')}} @endif</span>
						
				</div>

				<div class = 'form-group @if($errors->has("secondary_contact")) {{"has-error"}} @endif'>
					<label for = 'secondary_contact'  class = 'control-label'>Secondary Contact :</label>
						
					<input type = 'text' name = 'secondary_contact' value= '{{$data['data']->secondary_contact}}' class = 'form-control'>
					<span class = 'help-block'>@if($errors->has('secondary_contact')) {{$errors->first('secondary_contact')}} @endif</span>
						
				</div>

				<div class = 'form-group @if($errors->has("email")) {{"has-error"}} @endif'>
					<label for = 'email'  class = 'control-label'>Email :</label>
						
					<input type = 'text' name = 'email' value= '{{$data['data']->email}}' class = 'form-control'>
					<span class = 'help-block'>@if($errors->has('email')) {{$errors->first('email')}} @endif</span>
						
				</div>

				<div class = 'form-group @if($errors->has("joining_date_in_ad")) {{"has-error"}} @endif'>
					<label for = 'joining_date_in_ad'  class = 'control-label'>Joining Date In AD :</label>
						
					<input type = 'text' name = 'joining_date_in_ad' value= '{{$data['data']->joining_date_in_ad}}' class = 'form-control myDate'>
					<span class = 'help-block'>@if($errors->has('joining_date_in_ad')) {{$errors->first('joining_date_in_ad')}} @endif</span>
						
				</div>

				<div class = 'form-group @if($errors->has("leave_date_in_ad")) {{"has-error"}} @endif'>
					<label for = 'leave_date_in_ad'  class = 'control-label'>Leaving Date In AD :</label>
						
					<input type = 'text' name = 'leave_date_in_ad' value= '{{$data['data']->leave_date_in_ad}}' class = 'form-control myDate'>
					<span class = 'help-block'>@if($errors->has('leave_date_in_ad')) {{$errors->first('leave_date_in_ad')}} @endif</span>
						
				</div>

				{{-- <div class = 'form-group @if($errors->has("position")) {{"has-error"}} @endif'>
					<label for = 'position'  class = 'control-label'>Position :</label>
						
					<input type = 'text' name = 'position' value= '{{$data['data']->position}}' class = 'form-control'>
					<span class = 'help-block'>@if($errors->has('position')) {{$errors->first('position')}} @endif</span>
						
				</div> --}}

				<div class = 'form-group @if($errors->has("photo")) {{"has-error"}} @endif'>
					<label for = 'photo'  class = 'control-label'>Photo :</label>
						
					<div>
						@if(strlen(trim($data['data']->photo)))
							<img width="250px" height="auto" src = "{{Config::get('app.url').'app/modules/employee/assets/images/'.$data['data']->photo}}">
							<br/><br/>
						@else
							<p>No image available</p>
						@endif
					</div>
					<input type = 'file' name = 'photo' >
					<input type = "hidden" name = "original_photo" value = "{{$data['data']->photo}}">
					<span class = 'help-block'>@if($errors->has('photo')) {{$errors->first('photo')}} @endif</span>
						
				</div>

				<div class = 'form-group @if($errors->has("cv")) {{"has-error"}} @endif'>
					
					<label for = 'cv'  class = 'control-label'>CV :</label>
					@if(strlen(trim($data['data']->cv)))
						<a href = "{{Config::get('app.url').'app/modules/employee/assets/cv/'.$data['data']->cv}}">{{$data['data']->cv}}</a>
					@else
						<p>No cv available</p>
					@endif
					<input type = 'file' name = 'cv'>
					<input type = "hidden" name = "original_cv" value = "{{$data['data']->photo}}">
					<span class = 'help-block'>@if($errors->has('cv')) {{$errors->first('cv')}} @endif</span>
						
				</div>

				<div class = 'form-group @if($errors->has("group_id")) {{"has-error"}} @endif'>
					<label for = 'password'  class = 'control-label'>Select Role :&nbsp;&nbsp;&nbsp;</label>
					<!-- make foreach statement here  -->
					@foreach($posts as $group_id => $p)
						<span>
							<input type = "checkbox" name = "group_id[]" value = "{{$group_id}}" class = "eton_group" @if(isset($data['groups'][$group_id])) checked @endif />&nbsp;&nbsp;{{$p}}&nbsp;&nbsp;&nbsp;
						</span>
					@endforeach
				</div>

				<div class = 'form-group @if($errors->has("is_working")) {{"has-error"}} @endif'>
					<label for = 'section_code'  class = 'control-label'>Is Working: &nbsp;&nbsp;&nbsp;</label>
					<input type = 'radio' name = 'is_working' value = 'yes' @if($data['data']->is_working == 'yes') {{'checked'}} @endif>&nbsp;&nbsp;Yes&nbsp;&nbsp;&nbsp;
					<input type = 'radio' name = 'is_working' value = 'no' @if($data['data']->is_working == 'no') {{'checked'}} @endif>&nbsp;&nbsp;No
				</div>

				<div class = 'form-group @if($errors->has("is_active")) {{"has-error"}} @endif'>
					<label for = 'section_code'  class = 'control-label'>Is Active:&nbsp;&nbsp;&nbsp;</label>
					<input type = 'radio' name = 'is_active' value = 'yes' @if($data['data']->is_active == 'yes') {{'checked'}} @endif>&nbsp;&nbsp;Yes&nbsp;&nbsp;&nbsp;
					<input type = 'radio' name = 'is_active' value = 'no' @if($data['data']->is_active == 'no') {{'checked'}} @endif>&nbsp;&nbsp;No
				</div>

				{{Form::token()}}
				<input type = "hidden" name = "id" value = "{{$data['data']->id}}">
				
				<div class="form-group">
		        	<button class="btn btn-success btn-flat btn-lg" type="submit">Submit</button>
		        </div>
		    
		    </form>  
		@else
			<h1>Record Not Found</h1>
		@endif                            
	</div>
     
@stop

@section('custom-js')
<script src = "{{ asset('backend-js/validation.js') }}" type = "text/javascript"></script>
<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "text/javascript"></script>

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
