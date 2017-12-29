@extends('include.form-tabs')
@section('custom-css')
 <link href="{{asset('sms/plugins/daterangepicker/daterangepicker-bs3-event.css')}}" rel="stylesheet" type="text/css" />
 <link href="{{asset('sms/plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css" />
@stop
@section('tab-content')

{{-- $actionButtons --}}

<div class = 'content'>
	<form method = "post" action = "{{URL::route($module_name.'-create-post')}}"  class = "form-horizontal" id = "backendForm">

		<div class = 'form-group @if($errors->has("session_name")) {{"has-error"}} @endif'>
			<label for = 'session_name'>Session Name:</label>
			<input type = 'text' name = 'session_name' value= '{{ (Input::old('session_name')) ? (Input::old('session_name')) : '' }}' class = 'form-control required'><span class = 'help-block'>@if($errors->has('session_name')) {{$errors->first('session_name')}} @endif</span>
		</div>
	
		<div class = 'form-group @if($errors->has("session_start_date_in_ad")) {{"has-error"}} @endif'>
			<label for = 'session_start_date_in_ad' >Session Start (AD):</label>

			<input type = 'text' name = 'session_start_date_in_ad' value= '{{ (Input::old('session_start_date_in_ad')) ? (Input::old('session_start_date_in_ad')) : '' }}' class = 'form-control myDate required'>

			<span class = 'help-block'>@if($errors->has('session_start_date_in_ad')) {{$errors->first('session_start_date_in_ad')}} @endif</span>
		</div>
		
		<div class = 'form-group @if($errors->has("session_end_date_in_ad")) {{"has-error"}} @endif'>
			<label for = 'session_end_date_in_ad'>Session End (AD):</label>
			<input type = 'text' name = 'session_end_date_in_ad' value= '{{ (Input::old('session_end_date_in_ad')) ? (Input::old('session_end_date_in_ad')) : '' }}' class = 'form-control myDate required'><span class = 'help-block'>@if($errors->has('session_end_date_in_ad')) {{$errors->first('session_end_date_in_ad')}} @endif</span>
		</div>
		
		<div class = 'form-group @if($errors->has("is_current")) {{"has-error"}} @endif'>
			<label for = 'is_current'>Is Current :</label>&nbsp;&nbsp;&nbsp;
				
				<input type = 'radio' name = 'is_current' value='yes' {{ (Input::old('is_current')=='yes') ? 'checked' : '' }}>&nbsp;&nbsp;Yes&nbsp;&nbsp;&nbsp;
				
				
				<input type = 'radio' name = 'is_current' value='no' {{ (!Input::old('is_current') || Input::old('is_current')=='no') ? 'checked' : '' }}>&nbsp;&nbsp;No
				

				<span class = 'help-block'>@if($errors->has('is_current')) {{$errors->first('is_current')}} @endif</span>
		</div>

		<input type = 'hidden' name = 'is_active' value = 'yes'>
		<div class="form-group">
			{{Form::token()}}
		<button class="btn btn-success btn-lg btn-flat submit-enable-disable" type="submit" related-form = "backendForm">Submit</button>
		</div>
	</form>
</div>

@stop

@section('custom-js')

<script src = "{{ asset('backend-js/submit-enable-disable.js') }}" type = "text/javascript"></script>
<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "text/javascript"></script>


<script src = "{{ asset('backend-js/validation.js') }}" type = "text/javascript"></script>

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
