@extends('backend.'.$role.'.main')

@section('content')

{{$actionButtons}}

<div class = 'container'>
	<form method = "post" action = "{{URL::route($module_name.'-create-post')}}"  class = "form-horizontal" id = "backendForm">
	
		<div class = 'form-group @if($errors->has("module_name")) {{"has-error"}} @endif'>
			<label for = 'module_name'  class = 'control-label col-xs-2'>Module Name :</label>
				<div class = 'col-xs-10'>
			<input type = 'text' name = 'module_name' value= '{{ (Input::old('module_name')) ? (Input::old('module_name')) : '' }}' class = 'form-control required'><span class = 'help-block'>@if($errors->has('module_name')) {{$errors->first('module_name')}} @endif</span>
				</div>
		</div>
		<div class = 'form-group @if($errors->has("module_alias")) {{"has-error"}} @endif'>
			<label for = 'module_alias'  class = 'control-label col-xs-2'>Module Alias :</label>
				<div class = 'col-xs-10'>
			<input type = 'text' name = 'module_alias' value= '{{ (Input::old('module_alias')) ? (Input::old('module_alias')) : '' }}' class = 'form-control required'><span class = 'help-block'>@if($errors->has('module_alias')) {{$errors->first('module_alias')}} @endif</span>
				</div>
		</div>

		<input type = 'hidden' name = 'is_active' value = 'yes'>
		<div class = 'form-row'>
			<div module='col-xs-offset-2 col-xs-10'>
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
