@extends('backend.'. $current_user->role . '.main')

@section('content')
	<div class="content">
		<form method = "post" action = "{{URL::route($module_name.'-edit-post', $data->id)}}">
			
	    <div class="form-group">
	      <label>Select Session</label>
	      {{HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'academic_session_id', 
	        $selected = 
	          Input::has('academic_session_id') ?
	          Input::get('academic_session_id') : $data->academic_session_id)}}
	    </div>
			
			<div class = 'form-group @if($errors->has("student_id")) {{"has-error"}} @endif'>
				<label for = 'student_id'  class = 'control-label'>Student's Username :</label>
					
				<input type = 'text' name = 'student_id' value= '{{ (Input::old('student_id')) ? (Input::old('student_id')) : $data->student_id }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('student_id')) {{$errors->first('student_id')}} @endif</span>
			</div>

			<div class = 'form-group @if($errors->has("remarks")) {{"has-error"}} @endif'>
				<label for = 'remarks'  class = 'control-label'>Remarks :</label>
					
				<input type = 'text' name = 'remarks' value= '{{ (Input::old('remarks')) ? (Input::old('remarks')) : $data->remarks }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('remarks')) {{$errors->first('remarks')}} @endif</span>
					
			</div>

			<div class="form-group @if($errors->has("dormitory_id")) {{"has-error"}} @endif">
	      <label>Dormitory</label>
	      {{HelperController::generateSelectList('DormitoryRoom', 'dormitory_code', 'id', 'dormitory_id', 
	        $selected = 
	          Input::has('dormitory_id') ?
	          Input::get('dormitory_id') : $data->dormitory_id)}}
	    </div>

	    <div class="form-group @if($errors->has("type")) {{"has-error"}} @endif">
	      <label>Type</label>
	      @define $default_type = Input::old('type') ? Input::old('type') : $data->type
	      <select class="form-control" name='type'>
	        <option value='day' @if($default_type=='day') selected @endif >Day</option>
	        <option value='full' @if($default_type=='full') selected @endif>Full</option>
	      </select>
	    </div>

	    <div class = 'form-group @if($errors->has("fee_amount")) {{"has-error"}} @endif'>
				<label for = 'fee_amount'  class = 'control-label'>Fee :</label>
					
				<input type = 'text' name = 'fee_amount' value= '{{ (Input::old('fee_amount')) ? (Input::old('fee_amount')) : $data->fee_amount }}' class = 'form-control required'>
				<span class = 'help-block'>@if($errors->has('fee_amount')) {{$errors->first('fee_amount')}} @endif</span>
					
			</div>

			<input type="hidden" name="id" value="{{$data->id}}">
			<input type="hidden" name="is_active" value="yes">

			{{Form::token()}}
			
			<div class = "form-group">
				<input type = "submit" class = "btn btn-info" value = "Update">
			</div>

		</form>
	</div>
@stop
