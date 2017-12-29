@extends('backend.'.$role.'.main')

@section('custom-css')
 <link href="{{asset('sms/plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('content')

@if($data)
	<div class = 'content'>
	  <form method = "post" action = "{{URL::route($module_name.'-edit-post', $data->id)}}"></td>
	
		<div class = 'form-group @if($errors->has("bus_code")) {{"has-error"}} @endif'>
			<label>Bus Code</label>
			<input type = 'text' name = 'bus_code' value = '{{$data->bus_code}}' class = "required form-control">
			<span class = 'help-block'>@if($errors->has('bus_code')) {{$errors->first('bus_code')}} @endif</span>
		</div>

		<div class = 'form-group @if($errors->has("unique_transportation_id")) {{"has-error"}} @endif'>
			<label>Unique Transportation ID</label>
			<input type = 'text' name = 'unique_transportation_id' value = '{{$data->unique_transportation_id}}' class = "required form-control">
			<span class = 'help-block'>@if($errors->has('unique_transportation_id')) {{$errors->first('unique_transportation_id')}} @endif</span>
		</div>
		
		<div class = 'form-group @if($errors->has("number_plate")) {{"has-error"}} @endif'>
			<label>Number plate</label>
			<input type = 'text' name = 'number_plate' value = '{{$data->number_plate}}' class = "required form-control">
			<span class = 'help-block'>@if($errors->has('number_plate')) {{$errors->first('number_plate')}} @endif</span>
		</div>

		<div class = 'form-group @if($errors->has("route")) {{"has-error"}} @endif'>
			<label>Route</label>
			<textarea rows="3" name = 'route' class = "required form-control">{{$data->route}}</textarea>
			<span class = 'help-block'>@if($errors->has('route')) {{$errors->first('route')}} @endif</span>
		</div>
		<div class = 'form-group @if($errors->has("route_number")) {{"has-error"}} @endif'>
			<label for = 'route_number'  class = 'control-label'> Route Number</label>
			<input type = 'text' name = 'route_number' value= '{{ $data->route_number}}' class = 'form-control required'><span class = 

'help-block'>@if($errors->has('route_number')) {{$errors->first('route_number')}} @endif</span>
		</div>


		<div class = 'form-group @if($errors->has("driver_number")) {{"has-error"}} @endif'>
			<label>Driver's Phone Number</label>
			<input type = 'text' name = 'driver_number' value = '{{$data->driver_number}}' class = "required form-control">
			<span class = 'help-block'>@if($errors->has('driver_number')) {{$errors->first('driver_number')}} @endif</span>
		</div>

		<div class="form-group">
			<label>Active</label>&nbsp;&nbsp;&nbsp;
			<span><input type = 'radio' name = 'is_active' value = 'yes' @if($data->is_active == 'yes') {{'checked'}} @endif>&nbsp;&nbsp;Yes&nbsp;&nbsp;&nbsp;</span>
			<span><input type = 'radio' name = 'is_active' value = 'no' @if($data->is_active == 'no') {{'checked'}} @endif>&nbsp;&nbsp;No</span>
		</div>
		
		{{Form::token()}}<input type = "submit" class = "btn btn-success btn-lg btn-flat" value = "Update">

		<a href = "{{URL::route($module_name.'-list')}}" class = "btn btn-danger btn-lg btn-flat">Cancel</a>

		<input type = "hidden" name = "id" value = "{{$data->id}}">

		</form>

	</div>
@else
	<h4 class="text-red">No Record Found</h4>
@endif

@stop

@section('custom-js')
<script src = "{{ asset('backend-js/validation.js') }}" type = "javascript/text"></script>
<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "javascript/text"></script>

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


