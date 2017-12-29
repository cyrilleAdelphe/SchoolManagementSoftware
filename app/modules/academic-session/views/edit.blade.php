@extends('academic-session.views.form-tabs')



@section('custom-css')
 <link href="{{asset('sms/plugins/daterangepicker/daterangepicker-bs3.css')}}" rel="stylesheet" type="text/css" />
 <link href="{{asset('sms/plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('tab-content')

@if($data)
	<div class = 'content'>
		<div style="margin-bottom: 10px; display: block; clear: both; overflow: hidden">
  <a  href="#" onclick="history.go(-1);" class="btn btn-danger pull-right btn-flat"><i class="fa fa-fw fa-arrow-left"></i> Go Back</a>
</div>
		<form method = "post" action = "{{URL::route($module_name.'-edit-post', $data->id)}}"></td>
				
				<div class = 'form-group @if($errors->has("session_name")) {{"has-error"}} @endif'>
					<label>Session name</label>
					<input type = 'text' name = 'session_name' value = '{{$data->session_name}}' class = "required form-control"><span class = 'help-block'>@if($errors->has('session_name')) {{$errors->first('session_name')}} @endif</span>
				</div>

				<div class = 'form-group @if($errors->has("session_start_date_in_ad")) {{"has-error"}} @endif'>
					<label>Start date</label>
					<input type="text" class="form-control required sessionDate" name = 'session_start_date_in_ad' value="{{$data->session_start_date_in_ad}}" /><span class = 'help-block'>@if($errors->has('session_start_date_in_ad')) {{$errors->first('session_start_date_in_ad')}} @endif</span>

				</div>

				<div class = 'form-group @if($errors->has("session_end_date_in_ad")) {{"has-error"}} @endif'>
					<label>End date</label>
					<input type = 'text' name = 'session_end_date_in_ad' value = '{{$data->session_end_date_in_ad}}' class = "required form-control sessionDate"><span class = 'help-block'>@if($errors->has('session_end_date_in_ad')) {{$errors->first('session_end_date_in_ad')}} @endif</span>
				</div>

			<div class="form-group">
				<label>Is Active</label>
				<input type = 'radio' name = 'is_active' value = 'yes' @if($data->is_active == 'yes') {{'checked'}} @endif>Yes</span><span><input type = 'radio' name = 'is_active' value = 'no' @if($data->is_active == 'no') {{'checked'}} @endif>No
			</div>

			<div class = 'form-group @if($errors->has("is_current")) {{"has-error"}} @endif'>
				<label>Current</label>
				<input type = 'radio' name = 'is_current' value = 'yes' @if($data->is_current == 'yes') {{'checked'}} @endif>
				Yes
				<input type = 'radio' name = 'is_current' value = 'no' @if($data->is_current == 'no') {{'checked'}} @endif>
				No
				<span class = 'help-block'>@if($errors->has('is_current')) {{$errors->first('is_current')}} @endif</span>
			</div>

			<div class="form-group">
				{{Form::token()}}
				<input type = "submit" class = "btn btn-success btn-lg btn-flat" value = "Update">
			</div>
		<input type = "hidden" name = "id" value = "{{$data->id}}">
		</form>
	</div>
@else
	<h1>No Record Found</h1>
@endif

@stop

@section('custom-js')
<script src = "{{ asset('backend-js/validation.js') }}" type = "javascript/text"></script>
<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "javascript/text"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js" type="text/javascript"></script>
<script src="{{asset('sms/plugins/daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
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


