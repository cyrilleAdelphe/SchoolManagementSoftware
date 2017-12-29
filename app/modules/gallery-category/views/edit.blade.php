@extends('backend.'. $current_user->role . '.main')
@section('custom-css')
	<!-- Theme style -->    
    <link href="{{asset('sms/plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css" />
@stop
@section('page-header')    
  <h1>Edit Category</h1>
@stop
@section('content')
	<div style="margin-bottom: 10px; display: block; clear: both; overflow: hidden">
  <a  href="#" onclick="history.go(-1);" class="btn btn-danger pull-right btn-flat"><i class="fa fa-fw fa-arrow-left"></i> Go Back</a>
</div>
	<div class="content">
		<form method = "post" action = "{{URL::route($module_name.'-edit-post', array($id))}}">
			<div class = 'form-group @if($errors->has("title")) {{"has-error"}} @endif'>
				<label for = 'title'  class = 'control-label'>Title :</label>
					
				<input type = 'text' name = 'title' value= '{{ (Input::old('title')) ? (Input::old('title')) : $data->title }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('title')) {{$errors->first('title')}} @endif</span>
					
			</div>

			<div class = 'form-group @if($errors->has("description")) {{"has-error"}} @endif'>
				<label for = 'description'  class = 'control-label'>Description :</label>
					
				<input type = 'text' name = 'description' value= '{{ (Input::old('description')) ? (Input::old('description')) : $data->description }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('description')) {{$errors->first('description')}} @endif</span>
					
			</div>

			<div class = 'form-group @if($errors->has("is_active")) {{"has-error"}} @endif'>
				<label for = 'is_active'  class = 'control-label'>Is Active:</label>&nbsp;&nbsp;
				<input type = 'radio' name = 'is_active' value= 'yes' @if($data->is_active == 'yes') checked @endif>&nbsp;&nbsp;Yes&nbsp;&nbsp;&nbsp;<input type = 'radio' name = 'is_active' value= 'no' @if($data->is_active == 'no') checked @endif>&nbsp;&nbsp;No
				<span class = 'help-block'>@if($errors->has('is_active')) {{$errors->first('is_active')}} @endif</span>
			</div>

			
			<input type = "hidden" name = "id" value = "{{$data->id}}">

			{{Form::token()}}
			
			<div class = "form-group">
				<input type = "submit" class = "btn btn-success btn-lg btn-flat" value = "Update">
			</div>

		</form>
	</div>
@stop

@section('custom-js')

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
