@extends('backend.superadmin.main')

@section('custom')
<script src = "{{ asset('backend-js/validation.js') }}" type = "javascript/text"></script>
@stop

@section('content')

<div class = 'container'>
	<form method = "post" action = "{{URL::route('permissions-by-routename-create-post')}}"  class = "form-horizontal">
		
		<div class = 'form-row'>
			<div class='col-xs-offset-2 col-xs-10'>
			<label for = "group_type"></label>
				<span>{{$group_type}}</span>
			</div>
		</div>

		<div class = 'form-row'>
			<div class='col-xs-offset-2 col-xs-10'>
			<label for = "group_id"></label>
				<span>{{$group_id}}</span>
			</div>
		</div>

		<div class = 'form-row'>
			<div class='col-xs-offset-2 col-xs-10'>
			<label for = "route_name">Route Name</label>
				<span><input type = "text" name = "route_name"</span>
			</div>
		</div>

		<input type = 'hidden' name = 'is_active' value = 'yes'>
		<div class = 'form-row'>
			<div class='col-xs-offset-2 col-xs-10'>
			{{Form::token()}}
				<span><input type = 'submit' value = 'create' class = 'btn btn-default'></span>
			</div>
		</div>
	</form>

</div>

<?php echo  File::get(app_path().'/modules/superadmin/scripts/permission-by-routename-create-script.js'); ?>
@stop
