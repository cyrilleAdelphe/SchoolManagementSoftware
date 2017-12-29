@extends('academic-session.views.form-tabs')

@section('tab-content')
	@define $current_session = HelperController::getCurrentSession()
	<form action = "{{URL::route('academic-session-migrate-session-post')}}" method = "post">
		<div class = "form-group">
			<label for = "current_session">Curernt Session: </label>
			@define $current_session_name = AcademicSession::where('id', $current_session)->pluck('session_name')
			<input type = "hidden" name = "current_session" value = "{{$current_session}}">
			{{$current_session_name}}
		</div>

		<div class = "form-group">
			<label for = "current_session">Previous Session: </label>
			@define $previous_session = AcademicSession::where('id', '<', $current_session)->orderBy('id', 'DESC')->first();

			@if($previous_session)
				{{$previous_session->session_name}}
				<input type = "hidden" name = "previous_session" value = "{{$previous_session->id}}">
				{{Form::token()}}
				<br>
				<input type = "submit" class = "btn btn-success" value = "Migrate">
			@else
				<h1>No previous Session Found. You cannot migrate Session.</h1>
			@endif
		</div>
	</form>
@stop