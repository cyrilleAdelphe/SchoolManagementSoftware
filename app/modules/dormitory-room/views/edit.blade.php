@extends('backend.'. $current_user->role . '.main')

@section('content')
	
	<div class="content">
		<form method = "post" action = "{{URL::route($module_name.'-edit-post', array($id))}}">
			<div class = 'form-group @if($errors->has("dormitory_name")) {{"has-error"}} @endif'>
				<label for = 'dormitory_name'  class = 'control-label'>Dormitory Name :</label>
					
				<input type = 'text' name = 'dormitory_name' value= '{{ (Input::old('dormitory_name')) ? (Input::old('dormitory_name')) : $data->dormitory_name }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('dormitory_name')) {{$errors->first('dormitory_name')}} @endif</span>
					
			</div>

			<div class = 'form-group @if($errors->has("dormitory_code")) {{"has-error"}} @endif'>
				<label for = 'dormitory_code'  class = 'control-label'>Dormitory Code :</label>
					
				<input type = 'text' name = 'dormitory_code' value= '{{ (Input::old('dormitory_code')) ? (Input::old('dormitory_code')) : $data->dormitory_code }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('dormitory_code')) {{$errors->first('dormitory_code')}} @endif</span>
					
			</div>

			<div class = 'form-group @if($errors->has("dormitory_location")) {{"has-error"}} @endif'>
				<label for = 'dormitory_location'  class = 'control-label'>Dormitory Location :</label>
					
				<input type = 'text' name = 'dormitory_location' value= '{{ (Input::old('dormitory_location')) ? (Input::old('dormitory_location')) : $data->dormitory_location }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('dormitory_location')) {{$errors->first('dormitory_location')}} @endif</span>
					
			</div>

			<div class = 'form-group @if($errors->has("is_active")) {{"has-error"}} @endif'>
				<label for = 'is_active'  class = 'control-label'>Is Active:</label>
				<input type = 'radio' name = 'is_active' value= 'yes' @if($data->is_active == 'yes') checked @endif>Yes<input type = 'radio' name = 'is_active' value= 'no' @if($data->is_active == 'no') checked @endif>No
				<span class = 'help-block'>@if($errors->has('is_active')) {{$errors->first('is_active')}} @endif</span>
			</div>

			
			<input type = "hidden" name = "id" value = "{{$data->id}}">

			{{Form::token()}}
			
			<div class = "form-group">
				<input type = "submit" class = "btn btn-info" value = "Update">
			</div>

		</form>
	</div>
@stop
