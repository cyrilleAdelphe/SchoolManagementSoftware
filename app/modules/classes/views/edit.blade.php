@extends('backend.'.$role.'.main')

@section('content')

@if($data)
	<div class = 'content'>
		<div class="row">
                  <div class="col-sm-12" style="margin-bottom:15px">
                    <a  href="#" onclick="history.go(-1);" class="btn btn-danger btn-flat pull-right"><i class="fa fa-fw fa-arrow-left"></i> Go Back</a>
                </div><!-- row ends -->
            </div>
		<form method = "post" action = "{{URL::route($module_name.'-edit-post', $data->id)}}"></td>

      
      	<div class="form-group" '@if($errors->has("academic_session-id")) {{"has-error"}} @endif'>
	      	<label>Session</label>
	      	{{HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'academic_session_id', $selected = $data->academic_session_id)}}
    	</div>
		<div class = 'form-group @if($errors->has("class_name")) {{"has-error"}} @endif'>
			<label>Class</label>
			<input type = 'text' name = 'class_name' value = '{{$data->class_name}}' class = "form-control required">
			<span class = 'help-block'>@if($errors->has('class_name')) {{$errors->first('class_name')}} @endif</span>
		</div>

		<div class = 'form-group @if($errors->has("class_code")) {{"has-error"}} @endif'>
			<label>Code</label>
			<input type = 'text' name = 'class_code' value = '{{$data->class_code}}' class = "form-control required">
			<span class = 'help-block'>@if($errors->has('class_code')) {{$errors->first('class_code')}} @endif</span>
		</div>

		<div class = 'form-group @if($errors->has("sort_order")) {{"has-error"}} @endif'>
			<label>Display order</label>
			<input type = 'text' name = 'sort_order' value = '{{$data->sort_order}}' class = "form-control required">
			<span class = 'help-block'>@if($errors->has('sort_order')) {{$errors->first('sort_order')}} @endif</span>
		</div>

		<div class = 'form-group'>
			<label>Active</label><br/>
			<input type = 'radio' name = 'is_active' value = 'yes' @if($data->is_active == 'yes') {{'checked'}} @endif> &nbsp;Yes &nbsp; &nbsp;&nbsp;
			<input type = 'radio' name = 'is_active' value = 'no' @if($data->is_active == 'no') {{'checked'}} @endif> &nbsp; No
		</div>
		
		{{Form::token()}}

		<div class = 'form-group'>
			<input type = "submit" class = "btn btn-success btn-lg btn-flat" value = "Update">
		</div>

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


