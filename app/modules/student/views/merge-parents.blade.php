@extends('student.views.form-tabs')

@section('tab-content')
	<form method = "post" action = "{{URL::route('student-merge-parents-post')}}" id = "backendForm" enctype = "multipart/form-data">

		<div class = 'form-group @if($errors->has("excel_file")) {{"has-error"}} @endif'>
        <label for = 'excel_file'  class = 'control-label'>Upload Excel File</label>
        <input type = 'file' name = 'excel_file'><span class = 'help-block'>@if($errors->has('excel_file')) {{$errors->first('excel_file')}} @endif</span>
    </div>

    <input type = "hidden" name = "registered_session_id" value = "{{ HelperController::getCurrentSession() }}" id = "registered_session_id" />

    <div class="form-group">
        <button class="btn btn-success btn-flat btn-lg" type="submit">Submit</button>
    </div>

    {{ Form::token() }}
	</form>
@stop

