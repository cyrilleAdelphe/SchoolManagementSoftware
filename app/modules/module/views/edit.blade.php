@extends('backend.'.$role.'.main')

@section('content')

{{$actionButtons}}

@if($data)
	<div class = 'container'>
		<form method = "post" action = "{{URL::route($module_name.'-edit-post', $data->id)}}"></td>
		<table class = "table table-striped table-hover table-bordered">
			<tbody>
			<tr>
				<th>Module Name</th>
				<td><div class = 'form-group @if($errors->has("module_name")) {{"has-error"}} @endif'><input type = 'text' name = 'module_name' value = '{{$data->module_name}}' class = "required"><span class = 'help-block'>@if($errors->has('module_name')) {{$errors->first('module_name')}} @endif</span></div></td>
			</tr>

			<tr>
				<th>Module Code</th>
				<td><div class = 'form-group @if($errors->has("module_alias")) {{"has-error"}} @endif'><input type = 'text' name = 'module_alias' value = '{{$data->module_alias}}' class = "required"><span class = 'help-block'>@if($errors->has('module_alias')) {{$errors->first('module_alias')}} @endif</span></div></td>
			</tr>
			
			<tr>
				<th>Is Active</th>
				<td><span><input type = 'radio' name = 'is_active' value = 'yes' @if($data->is_active == 'yes') {{'checked'}} @endif>Yes</span><span><input type = 'radio' name = 'is_active' value = 'no' @if($data->is_active == 'no') {{'checked'}} @endif>No</span>
			</tr>
			<tr>
				<td>{{Form::token()}}<input type = "submit" class = "btn btn-info" value = "edit"></td><td><a href = "{{URL::route($module_name.'-list')}}" class = "btn btn-default">Cancel</a></td>
			</tr>
			</body>
		</table>
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
@stop


