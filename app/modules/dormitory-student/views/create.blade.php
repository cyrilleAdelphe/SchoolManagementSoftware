@extends('dormitory-room.views.tabs')

@section('tab-content')
	<div class="content">
		<form method = "post" action = "{{URL::route($module_name.'-create-post')}}">
			
	    <div class="form-group">
	      <label>Select Session</label>
	      {{HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'academic_session_id', 
	        $selected = 
	          Input::has('academic_session_id') ?
	          Input::get('academic_session_id') : AcademicSession::where('is_current','yes')->first()['id'])}}
	    </div>
			
			<div class = 'form-group @if($errors->has("student_id")) {{"has-error"}} @endif'>
				<label for = 'student_id'  class = 'control-label'>Student's Username :</label>
					
				<input type = 'text' name = 'student_id' id = "studentUsername" value= '{{ (Input::old('student_id')) ? (Input::old('student_id')) : '' }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('student_id')) {{$errors->first('student_id')}} @endif</span>
				<a class="btn btn-info btn-sm" data-toggle="modal" data-target="#find-id" ><i class="fa fa-search"></i> Find ID</a>
      	@include('include.modal-find-student')
			</div>

			<div class = 'form-group @if($errors->has("remarks")) {{"has-error"}} @endif'>
				<label for = 'remarks'  class = 'control-label'>Remarks :</label>
					
				<input type = 'text' name = 'remarks' value= '{{ (Input::old('remarks')) ? (Input::old('remarks')) : '' }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('remarks')) {{$errors->first('remarks')}} @endif</span>
					
			</div>

			<div class="form-group @if($errors->has("dormitory_id")) {{"has-error"}} @endif">
	      <label>Dormitory</label>
	      {{HelperController::generateSelectList('DormitoryRoom', 'dormitory_code', 'id', 'dormitory_id', 
	        $selected = 
	          Input::old('dormitory_id') ?
	          Input::old('dormitory_id') : '')}}
	      <span class = 'help-block'>@if($errors->has('dormitory_id')) {{$errors->first('dormitory_id')}} @endif</span>
	    </div>

	    <div class="form-group @if($errors->has("type")) {{"has-error"}} @endif">
	      <label>Type</label>
	      <select class="form-control" name='type'>
	        <option value='day' @if(Input::old('type')=='day') selected @endif >Day</option>
	        <option value='full' @if(Input::old('type')=='full') selected @endif>Full</option>
	      </select>
	    </div>

	    <div class = 'form-group @if($errors->has("fee_amount")) {{"has-error"}} @endif'>
				<label for = 'fee_amount'  class = 'control-label'>Fee <small>(leave empty to assign default)</small> :</label>
					
				<input type = 'text' name = 'fee_amount' value= '{{ (Input::old('fee_amount')) ? (Input::old('fee_amount')) : '' }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('fee_amount')) {{$errors->first('fee_amount')}} @endif</span>
					
			</div>

			<input type="hidden" name="is_active" value="yes">

			{{Form::token()}}
			
			<div class = "form-group">
				<input type = "submit" class = "btn btn-info" value = "Create">
			</div>

		</form>
	</div>
@stop

@section('custom-js')
	<script type="text/javascript">
    // event trigger for select button after searching student
    function findIdSelect(username) {
      $('#studentUsername').val(username);
    }
  </script>
@stop