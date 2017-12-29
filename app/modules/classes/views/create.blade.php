@extends('include.form-tabs')

@section('tab-content')

<div class = 'content'>
	<form method = "post" action = "{{URL::route($module_name.'-create-post')}}"  class = "form-horizontal" id = "backendForm">

		
    <div class="form-group" '@if($errors->has("academic_session-id")) {{"has-error"}} @endif'>
      
      <label>Select Session</label>
      {{HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'academic_session_id', $selected = AcademicSession::where('is_current','yes')->first()['id'])}}
    </div>
	  
	
		<div class = 'form-group @if($errors->has("class_name")) {{"has-error"}} @endif'>
			<label for = 'class_name'  class = 'control-label'>Class Name :</label>
			<input type = 'text' name = 'class_name' value= '{{ (Input::old('class_name')) ? (Input::old('class_name')) : '' }}' class = 'form-control required'><span class = 'help-block'>@if($errors->has('class_name')) {{$errors->first('class_name')}} @endif</span>
		</div>
		
		<div class = 'form-group @if($errors->has("class_code")) {{"has-error"}} @endif'>
			<label for = 'class_code'  class = 'control-label'>Class Code :</label>
			<input type = 'text' name = 'class_code' value= '{{ (Input::old('class_code')) ? (Input::old('class_code')) : '' }}' class = 'form-control required'><span class = 'help-block'>@if($errors->has('class_code')) {{$errors->first('class_code')}} @endif</span>
		</div>

		<div class = 'form-group @if($errors->has("sort_order")) {{"has-error"}} @endif'>
			<label for = 'sort_order'  class = 'control-label'>Sort order :</label>
			<input type = 'text' name = 'sort_order' value= '{{ (Input::old('sort_order')) ? (Input::old('sort_order')) : '' }}' class = 'form-control required'><span class = 'help-block'>@if($errors->has('sort_order')) {{$errors->first('sort_order')}} @endif</span>
		</div>
		
		<input type = 'hidden' name = 'is_active' value = 'yes'>
		<div class = 'form-row'>
			<div class='col-xs-offset-2 col-xs-10'>
			{{Form::token()}}
			</div>
		</div>
		<div class="form-group">
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
