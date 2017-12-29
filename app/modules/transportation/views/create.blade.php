@extends('backend.'.$role.'.main')

@section('content')


<div class = 'content'>
	{{$actionButtons}}

	<form method = "post" action = "{{URL::route($module_name.'-create-post')}}" id = "backendForm">
	
		<div class = 'form-group @if($errors->has("bus_code")) {{"has-error"}} @endif'>
			<label for = 'bus_code'  class = 'control-label'>Bus Code:</label>
			<input type = 'text' name = 'bus_code' value= '{{ (Input::old('bus_code')) ? (Input::old('bus_code')) : '' }}' class = 'form-control required'>
			<span class = 'help-block'>@if($errors->has('bus_code')) {{$errors->first('bus_code')}} @endif</span>
		</div>

		<div class = 'form-group @if($errors->has("unique_transportation_id")) {{"has-error"}} @endif'>
			<label for = 'unique_transportation_id'  class = 'control-label'>Unique Transportation ID :</label>
			<input type = 'text' name = 'unique_transportation_id' value= '{{ (Input::old('unique_transportation_id')) ? (Input::old('unique_transportation_id')) : '' }}' class = 'form-control required'><span class = 'help-block'>@if($errors->has('unique_transportation_id')) {{$errors->first('unique_transportation_id')}} @endif</span>
		</div>

		<div class = 'form-group @if($errors->has("number_plate")) {{"has-error"}} @endif'>
			<label for = 'number_plate'  class = 'control-label'>Number Plate:</label>
			<input type = 'text' name = 'number_plate' value= '{{ (Input::old('number_plate')) ? (Input::old('number_plate')) : '' }}' class = 'form-control required'><span class = 'help-block'>@if($errors->has('number_plate')) {{$errors->first('number_plate')}} @endif</span>
		</div>


		<div class = 'form-group @if($errors->has("route")) {{"has-error"}} @endif'>
			<label for = 'route'  class = 'control-label'>Route:</label>
			<textarea name = 'route' class = 'form-control required'>{{ (Input::old('route')) ? (Input::old('route')) : '' }}</textarea>
			<span class = 'help-block'>@if($errors->has('route')) {{$errors->first('route')}} @endif</span>
		</div>
		<div class = 'form-group @if($errors->has("route_number")) {{"has-error"}} @endif'>
			<label for = 'route_number'  class = 'control-label'> Route Number</label>
			<input type = 'text' name = 'route_number' value= '{{ (Input::old('route_number')) ? (Input::old('route_number')) : '' }}' 

class = 'form-control required'><span class = 'help-block'>@if($errors->has('route_number')) {{$errors->first('route_number')}} @endif</span>
		</div>


		<div class = 'form-group @if($errors->has("driver_number")) {{"has-error"}} @endif'>
			<label for = 'driver_number'  class = 'control-label'>Driver Phone:</label>
			<input type = 'text' name = 'driver_number' value= '{{ (Input::old('driver_number')) ? (Input::old('driver_number')) : '' }}' class = 'form-control required'>
			<span class = 'help-block'>@if($errors->has('driver_number')) {{$errors->first('driver_number')}} @endif</span>
		</div>
		
		<input type = 'hidden' name = 'is_active' value = 'yes'>
		<div class = 'form-row'>
			<div class='col-xs-offset-2 col-xs-10'>
			{{Form::token()}}
			</div>
		</div>
	</form>
</div>

@stop

@section('custom-js')

<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "text/javascript"></script>


<script src = "{{ asset('backend-js/validation.js') }}" type = "text/javascript"></script>

@stop
