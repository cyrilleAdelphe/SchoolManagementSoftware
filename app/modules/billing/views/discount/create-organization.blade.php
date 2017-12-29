@extends('backend.'.$role.'.main')

@section('content')

	<form method = "post" action = "{{URL::route('billing-discount-organization-create-post')}}">
	<div class = "row">
		<div>
			<label>Organization Name</label>
			<input type = "text" name = "organization_name">
		</div>

		<div>
			<label>Generate Invoice</label>
			<input type = "radio" name = "generate_invoice" value = "yes" checked>Yes<input type = "radio" name = "generate_invoice" value = "no">No
		</div>
		<div>
			<input type = "hidden" name = "is_active" value = "yes">
			<input type = "submit" class = "btn btn-success" value = "Create">
		</div>
	</div>
	{{Form::token()}}
	</form>

@stop