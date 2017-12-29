@extends('backend.'.$role.'.main')

@section('content')

{{$actionButtons}}

@if($data)
	<div class = 'container'>
		<form method = "post" action = "{{URL::route($module_name.'-edit-post', $data->id)}}"></td>
		<table class = "table table-striped table-hover table-bordered">
			<tbody>
			<tr>
				<th>Template Name : </th>
				<td><div class = 'form-group @if($errors->has("template_name")) {{"has-error"}} @endif'><input type = 'text' name = 'template_name' value = '{{$data->template_name}}' class = "required"><span class = 'help-block'>@if($errors->has('template_name')) {{$errors->first('template_name')}} @endif</span></div></td>
			</tr>

			<tr>
				<th>Template Alias</th>
				<td><div class = 'form-group @if($errors->has("template_alias")) {{"has-error"}} @endif'><input type = 'text' name = 'template_alias' value = '{{$data->template_alias}}' class = "required"><span class = 'help-block'>@if($errors->has('template_alias')) {{$errors->first('template_alias')}} @endif</span></div></td>
			</tr>

			<tr>
				<th>Position</th>
				<td>@foreach($positions as $p)
						<div>
							<input type = "checkbox" name = "position_id[]" value = "{{$p->id}}" class = "position_id" @if(isset($selected_positions[$p->id])) checked @endif>{{$p->position_name}}<label>Sort Order: </label><input type = "text" name = "sort_order[]" class = "eton_sort_order_disabled" value = "@if(isset($selected_positions[$p->id])) {{$selected_positions[$p->id]}} @endif">
						</div>
					@endforeach
				</td>
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
{{File::get(app_path().'/modules/templates/assets/js/create-edit.js')}}
@stop


