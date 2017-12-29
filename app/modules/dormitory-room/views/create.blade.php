@extends('dormitory-room.views.tabs')

@section('tab-content')
	<div class="content">
		<form method = "post" action = "{{URL::route($module_name.'-create-post')}}">
			<div class = 'form-group @if($errors->has("dormitory_name")) {{"has-error"}} @endif'>
				<label for = 'dormitory_name'  class = 'control-label'>Dormitory Name :</label>
					
				<input type = 'text' name = 'dormitory_name' value= '{{ (Input::old('dormitory_name')) ? (Input::old('dormitory_name')) : '' }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('dormitory_name')) {{$errors->first('dormitory_name')}} @endif</span>
			</div>

			<div class = 'form-group @if($errors->has("dormitory_code")) {{"has-error"}} @endif'>
				<label for = 'dormitory_code'  class = 'control-label'>Dormitory Code :</label>
					
				<input type = 'text' name = 'dormitory_code' value= '{{ (Input::old('dormitory_code')) ? (Input::old('dormitory_code')) : '' }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('dormitory_code')) {{$errors->first('dormitory_code')}} @endif</span>
					
			</div>

			<div class = 'form-group @if($errors->has("dormitory_location")) {{"has-error"}} @endif'>
				<label for = 'dormitory_location'  class = 'control-label'>Location :</label>
					
				<input type = 'text' name = 'dormitory_location' value= '{{ (Input::old('dormitory_location')) ? (Input::old('dormitory_location')) : '' }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('dormitory_location')) {{$errors->first('dormitory_location')}} @endif</span>
					
			</div>

			<input type="hidden" name="is_active" value="yes">

			{{Form::token()}}
			
			<div class = "form-group">
				<input type = "submit" class = "btn btn-info" value = "Create">
			</div>

		</form>
	</div>
@stop
