@extends('employee.views.form-tabs')

@section('tab-content')
	<form method = "get" action = "{{ Request::url() }}" id = "findEmployee">
		<div class = "form-group">
			<label> Employee Username: </label>
			<input type = "text" name = "employee_username" id = "employeeUsername" value = "{{ Input::get('employee_username', '') }}" />
			<a class="btn btn-info btn-sm" data-toggle="modal" data-target="#find-id" ><i class="fa fa-search"></i> Find ID</a>
	 		@include('include.modal-find-employee')
		</div>
		{{ Form::token() }}
	</form>
	
	@if (Input::has('employee_username'))
		@if ($data['employee'])
			<h3> Upload a File </h3>
			@include('employee-document.views.upload-file')


			<script src = "{{ asset('backend-js/validation.js') }}" type = "text/javascript"></script>
			<h3> Files </h3>
			@include('employee-document.views.list')
		@else
			<div class="alert alert-warning alert-dismissable">
          <h4><i class="icon fa fa-warning"></i>Invalid username</h4>
      </div>
		@endif
	@endif
@stop

@section('custom-js')

	<script type="text/javascript">

		// event trigger for select button after searching employee
		function findIdSelect(username) {
		  $('#employeeUsername').val(username);
		  $('#findEmployee').submit();
		}

	</script>

@stop