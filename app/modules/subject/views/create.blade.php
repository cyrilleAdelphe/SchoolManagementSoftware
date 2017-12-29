@extends('subject.views.tabs')

@section('tab-content')


	<form method = "post" action = "{{URL::route($module_name.'-create-post')}}"  id = "backendForm">

		<div class="row">
			<div class="col-sm-6">
				<div class = 'form-group @if($errors->has("subject_name")) {{"has-error"}} @endif'>
					<label for = 'subject_name'  class = 'control-label'>Name</label>
						
					<input type = 'text' name = 'subject_name' value= '{{ (Input::old('subject_name')) ? (Input::old('subject_name')) : '' }}' class = 'form-control required'><span class = 'help-block'>@if($errors->has('subject_name')) {{$errors->first('subject_name')}} @endif</span>
				</div>
			</div>
			<div class="col-sm-6">
				<div class = 'form-group @if($errors->has("subject_code")) {{"has-error"}} @endif'>
					<label for = 'subject_code'  class = 'control-label'>Code</label>
						
					<input type = 'text' name = 'subject_code' value= '{{ (Input::old('subject_code')) ? (Input::old('subject_code')) : '' }}' class = 'form-control required'><span class = 'help-block'>@if($errors->has('subject_code')) {{$errors->first('subject_code')}} @endif</span>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6">
				<div class = 'form-group @if($errors->has("full_marks")) {{"has-error"}} @endif'>
					<label for = 'full_marks'  class = 'control-label'>Full Marks</label>
						
					<input type = 'text' name = 'full_marks' value= '{{ (Input::old('full_marks')) ? (Input::old('full_marks')) : '' }}' class = 'form-control required'><span class = 'help-block'>@if($errors->has('full_marks')) {{$errors->first('full_marks')}} @endif</span>
				</div>
			</div>
			<div class="col-sm-6">
				<div class = 'form-group @if($errors->has("pass_marks")) {{"has-error"}} @endif'>
					<label for = 'pass_marks'  class = 'control-label'>Pass Marks</label>
						
					<input type = 'text' name = 'pass_marks' value= '{{ (Input::old('pass_marks')) ? (Input::old('pass_marks')) : '' }}' class = 'form-control required'><span class = 'help-block'>@if($errors->has('pass_marks')) {{$errors->first('pass_marks')}} @endif</span>
				</div>
			</div>
		</div>

		<div class = 'form-group @if($errors->has("remarks")) {{"has-error"}} @endif'>
			<label for = 'remarks'  class = 'control-label'>Remarks</label>				
			<input type = 'text' name = 'remarks' value= '{{ (Input::old('remarks')) ? (Input::old('remarks')) : '' }}' class = 'form-control required' /><span class = 'help-block'>@if($errors->has('remarks')) {{$errors->first('remarks')}} @endif</span>
		</div>

		<div class="row">
		  <div class="col-sm-4">
		    <div class="form-group">
		      <label>Select Session</label>
		      {{HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'academic_session_id', 
		      	$selected = 
		      		Input::has('academic_session_id') ?
		      		Input::get('academic_session_id') : AcademicSession::where('is_current','yes')->first()['id'])}}
		    </div>
		  </div>

		  <div class="col-sm-4">
		    <div class="form-group">
		      <label>Select class</label>
		      <select name="class_id" id="class_id" class="form-control">
						<option value="0">--Select Session First--</option>
			  </select>
		    </div>
		  </div>

		  <div class="col-sm-4">
		    <div class="form-group">
		      <label>Select section</label>
		      <select name="section_id" id="section_id" class="form-control">
				<option value="0">--Select Class First--</option>
			  </select>
		    </div>
		  </div>
		</div>

		<div class="form-group @if($errors->has('sort_order')) {{'has-error'}} @endif">
      	<label>Sort Order</label>
     		<input name="sort_order" id="sort_order" class="form-control" value = "{{ (Input::old('sort_order')) ? (Input::old('sort_order')) : '' }}">
			<span class = 'help-block'>@if($errors->has('sort_order')) {{$errors->first('sort_order')}} @endif</span>
   	</div>

   	<div class="form-group @if($errors->has('is_graded')) {{'has-error'}} @endif">
      	<label>Is Graded</label>
     		<span>
     			<input type = 'radio' name = 'is_graded' value = 'yes' @if(Input::old('is_graded') == 'yes' || !Input::old('is_graded')) {{'checked'}} @endif>Yes
     		</span>

     		<span>
     			<input type = 'radio' name = 'is_graded' value = 'no' @if(Input::old('is_graded') == 'no') {{'checked'}} @endif>No
     		</span>
			<span class = 'help-block'>@if($errors->has('is_graded')) {{$errors->first('is_graded')}} @endif</span>
   	</div>

   	<div class="form-group @if($errors->has('include_in_report_card')) {{'has-error'}} @endif">
      	<label>Include In Report Card</label>
     		<span>
     			<input type = 'radio' name = 'include_in_report_card' value = 'yes' @if(Input::old('include_in_report_card') == 'yes' || !Input::old('include_in_report_card')) {{'checked'}} @endif>Yes
     		</span>

     		<span>
     			<input type = 'radio' name = 'include_in_report_card' value = 'no' @if(Input::old('include_in_report_card') == 'no') {{'checked'}} @endif>No
     		</span>
			<span class = 'help-block'>@if($errors->has('include_in_report_card')) {{$errors->first('include_in_report_card')}} @endif</span>
   	</div>
		
		<input type = 'hidden' name = 'is_active' value = 'yes'>
		<div class = 'form-row'>
			<div subject='col-xs-offset-2 col-xs-10'>
			{{Form::token()}}
			</div>
		</div>

		<div class="form-group">
      		<button class="btn btn-success btn-lg btn-flat submit-enable-disable" type="submit" related-form="backendForm">Submit</button>
    	</div>
    	
	</form>

<input type="hidden" id="class_ajax" value="{{URL::route('ajax-classes-get-classes')}}" />

<input type="hidden" id="section_ajax" value="{{URL::route('ajax-attendance-get-class-section')}}" />

<input type="hidden" id="teacher_ajax" value="{{URL::route('ajax-subject-get-teachers')}}" />

<input type="hidden" name= "default_academic_session" id="default_academic_session" value="{{Input::has('academic_session_id')?Input::get('academic_session_id'):''}}" />

@stop

@section('custom-js')

<script src = "{{ asset('backend-js/submit-enable-disable.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/validation.js') }}" type = "text/javascript"></script>

<script src="{{ asset('backend-js/getStaticSelectList.js') }}" type="text/javascript"></script>

<script src="{{ asset('backend-js/updateClassList.js') }}" type="text/javascript"></script>

<script src="{{ asset('backend-js/updateSectionList.js') }}" type="text/javascript"></script>

<script src="{{ asset('backend-js/updateTeacherList.js') }}" type="text/javascript"></script>

<script>
	$(function() {

		if($('#academic_session_id').val() != 0)
		{
			updateClassList();
		}

		$(document).on('change', '#academic_session_id', updateClassList);

    $("#class_id").bind('change', function() {
    	updateSectionList();
    	updateTeacherList();
    });
    
    $("#section_id").bind('change', updateTeacherList);

	});
</script>


@stop