@extends('backend.'.$role.'.main')

@section('content')

{{$actionButtons}}

@if($data)
	<div class = 'content'>
		<form method = "post" action = "{{URL::route($module_name.'-edit-post', $data->id)}}"></td>
		<table class = "table table-striped table-hover table-bordered">
			<tbody>
			<tr>
				<th>Subject Name</th>
				<td><div class = 'form-group @if($errors->has("subject_name")) {{"has-error"}} @endif'><input type = 'text' name = 'subject_name' value = '{{$data->subject_name}}' class = "required"><span class = 'help-block'>@if($errors->has('subject_name')) {{$errors->first('subject_name')}} @endif</span></div></td>
			</tr>

			<tr>
				<th>Subject Code</th>
				<td><div class = 'form-group @if($errors->has("subject_code")) {{"has-error"}} @endif'><input type = 'text' name = 'subject_code' value = '{{$data->subject_code}}' class = "required"><span class = 'help-block'>@if($errors->has('subject_code')) {{$errors->first('subject_code')}} @endif</span></div></td>
			</tr>
			<tr>
                <th>Full Marks</th>
                <td><div class = 'form-group @if($errors->has("full_marks")) {{"has-error"}} @endif'><input type = 'text' name = 'full_marks' value = '{{$data->full_marks}}' class = "required"><span class = 'help-block'>@if($errors->has('full_marks')) {{$errors->first('full_marks')}} @endif</span></div></td>
              </tr>

              <tr>
                <th>Pass Marks</th>
                <td><div class = 'form-group @if($errors->has("pass_marks")) {{"has-error"}} @endif'><input type = 'text' name = 'pass_marks' value = '{{$data->pass_marks}}' class = "required"><span class = 'help-block'>@if($errors->has('pass_marks')) {{$errors->first('pass_marks')}} @endif</span></div></td>
              </tr>

              <tr>
                <th>Remarks</th>
                <td><div class = 'form-group @if($errors->has("remarks")) {{"has-error"}} @endif'><input type = 'text' name = 'remarks' value = '{{$data->remarks}}' class = "required"><span class = 'help-block'>@if($errors->has('remarks')) {{$errors->first('remarks')}} @endif</span></div></td>
              </tr>

              <tr>
                <th>Sort Order</th>
                <td><div class = 'form-group @if($errors->has("sort_order")) {{"has-error"}} @endif'><input type = 'text' name = 'sort_order' value = '{{$data->sort_order}}' class = "required"><span class = 'help-block'>@if($errors->has('sort_order')) {{$errors->first('sort_order')}} @endif</span></div></td>
              </tr>
                  
                  
			<tr>
				<th>Is Active</th>
				<td><span><input type = 'radio' name = 'is_active' value = 'yes' @if($data->is_active == 'yes') {{'checked'}} @endif>Yes</span><span><input type = 'radio' name = 'is_active' value = 'no' @if($data->is_active == 'no') {{'checked'}} @endif>No</span>
			</tr>
			<tr>
				<th>Is Graded</th>
				<td><span><input type = 'radio' name = 'is_graded' value = 'yes' @if($data->is_graded == 'yes') {{'checked'}} @endif>Yes</span><span><input type = 'radio' name = 'is_graded' value = 'no' @if($data->is_graded == 'no') {{'checked'}} @endif>No</span>
			</tr>
			<tr>
				<th>Include In Report Card</th>
				<td><span><input type = 'radio' name = 'include_in_report_card' value = 'yes' @if($data->include_in_report_card == 'yes') {{'checked'}} @endif>Yes</span><span><input type = 'radio' name = 'include_in_report_card' value = 'no' @if($data->include_in_report_card == 'no') {{'checked'}} @endif>No</span>
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


