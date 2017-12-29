@extends('backend.'.$role.'.main')

@section('content')

@section('custom-css')
 <link href="{{asset('sms/plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css" />
@stop

@if($data)
	<div class = 'content'>
		<form method = "post" action = "{{URL::route('transportation-edit-assign-staffs-post', $data->id)}}">
			<div class = 'form-group @if($errors->has("transportation_id")) {{"has-error"}} @endif'>
				<label class="form-label">Bus Code</label> 
				{{HelperController::generateSelectList('Transportation', 'bus_code', 'id', 'transportation_id', $data->transportation_id)}}
                <span class = 'help-block'>@if($errors->has('transporation_id')) {{$errors->first('transporation_id')}} @endif</span>
			</div>

			<div class = 'form-group @if($errors->has("employee_id")) {{"has-error"}} @endif'>
				<label class="form-label">Employee's / Staff  ID</label>
				<input type = 'text' name = 'employee_id' value = '{{$employee_id}}' class = "required form-control">
				<span class = 'help-block'>@if($errors->has('employee_id')) {{$errors->first('employee_id')}} @endif</span>
			</div>
			
          	<div class="form-group">
          		<label>Active</label>&nbsp;&nbsp;&nbsp;
				<span>
					<input type = 'radio' name = 'is_active' value = 'yes' @if($data->is_active == 'yes') {{'checked'}} @endif>&nbsp;&nbsp;Yes
				</span>&nbsp;&nbsp;&nbsp;
				<span><input type = 'radio' name = 'is_active' value = 'no' @if($data->is_active == 'no') {{'checked'}} @endif>&nbsp;&nbsp;No</span>
			</div>
			
			{{Form::token()}}

			<input type = "submit" class = "btn btn-success btn-lg btn-flat" value = "Update">

			<a href = "{{URL::route('transportation-list')}}" class = "btn btn-danger btn-flat btn-lg">Cancel</a>

			<input type = "hidden" name = "id" value = "{{$data->id}}">
		</form>
	</div>
@else
	<h4 class="text-red">No Record Found</h4>
@endif

@stop

@section('custom-js')
<script src = "{{ asset('backend-js/validation.js') }}" type = "javascript/text"></script>
<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "javascript/text"></script>

<script src="{{ asset('sms/plugins/iCheck/icheck.min.js')}}" type="text/javascript"></script>
<script>
$(document).ready(function(){
  $('input').iCheck({
    checkboxClass: 'icheckbox_minimal-blue',
    radioClass: 'iradio_minimal-blue',
    increaseArea: '20%' // optional
  });
});
</script>

@stop


