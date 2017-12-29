@extends('include.form-tabs')

@section('tab-content')

<div class = 'container'>
	<form method = "post" action = "{{URL::route($module_name.'-create-post')}}"  class = "form-horizontal" id = "backendForm">
	
		<div class = 'form-group @if($errors->has("section_name")) {{"has-error"}} @endif'>
			<label for = 'section_name'  class = 'control-label'>Section Name :</label>
			<input type = 'text' name = 'section_name' value= '{{ (Input::old('section_name')) ? (Input::old('section_name')) : '' }}' class = 'form-control required'><span class = 'help-block'>@if($errors->has('section_name')) {{$errors->first('section_name')}} @endif</span>
		</div>
		<div class = 'form-group @if($errors->has("section_code")) {{"has-error"}} @endif'>
			<label for = 'section_code'  class = 'control-label'>Section Code :</label>
			<input type = 'text' name = 'section_code' value= '{{ (Input::old('section_code')) ? (Input::old('section_code')) : '' }}' class = 'form-control required'><span class = 'help-block'>@if($errors->has('section_code')) {{$errors->first('section_code')}} @endif</span>
		</div>
		
		<input type = 'hidden' name = 'is_active' value = 'yes'>
		<div class = 'form-group'>
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

@stop
