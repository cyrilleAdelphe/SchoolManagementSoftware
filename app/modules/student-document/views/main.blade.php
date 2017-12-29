@extends('student.views.form-tabs')

@section('tab-content')
	<form method = "get" action = "{{ Request::url() }}" id = "findStudent">
		<div class = "form-group">
			<label> Student Username: </label>
			<input type = "text" name = "student_username" id = "studentUsername" value = "{{ Input::get('student_username', '') }}" class="form-control" />
			<br/>
			<a class="btn btn-info btn-flat" data-toggle="modal" data-target="#find-id" ><i class="fa fa-search"></i> Find ID</a>
	 		@include('include.modal-find-student')
		</div>
		{{ Form::token() }}
	</form>
	
	@if (Input::has('student_username'))
		@if ($data['student'])
			<br/>
			<h4 class="text-green"> Upload a File </h4>
			@include('student-document.views.upload-file')


			<script src = "{{ asset('backend-js/validation.js') }}" type = "text/javascript"></script>
			<h3> Files </h3>
			@include('student-document.views.list')
		@else
			<div class="alert alert-warning alert-dismissable">
          <h4><i class="icon fa fa-warning"></i>Invalid username</h4>
      </div>
		@endif
	@endif
@stop

@section('custom-js')
	<script src = "{{ asset('backend-js/submit-enable-disable.js') }}"></script>
	<script type="text/javascript">

		// event trigger for select button after searching student
		function findIdSelect(username) {
		  $('#studentUsername').val(username);
		  $('#findStudent').submit();
		}

	</script>

@stop