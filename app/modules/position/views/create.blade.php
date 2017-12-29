@extends('backend.'.$role.'.main')

@section('content')

{{$actionButtons}}

<div class = 'container'>
	<form method = "post" action = "{{URL::route($module_name.'-create-post')}}"  class = "form-horizontal" id = "backendForm">
	
		<div class = 'form-group @if($errors->has("position_name")) {{"has-error"}} @endif'>
			<label for = 'position_name'  class = 'control-label col-xs-2'>Position Name :</label>
				<div class = 'col-xs-10'>
			<input type = 'text' name = 'position_name' value= '{{ (Input::old('position_name')) ? (Input::old('position_name')) : '' }}' class = 'form-control required'><span class = 'help-block'>@if($errors->has('position_name')) {{$errors->first('position_name')}} @endif</span>
				</div>
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
