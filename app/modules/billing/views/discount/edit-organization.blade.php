@extends('backend.'.$role.'.main')

@section('content')
@if($data)
	<form method = "post" action = "{{URL::route('billing-discount-organization-edit-post', $id)}}">
	<div class = "row">
		<div>
			<label>Organization Name</label>
			<input type = "text" name = "organization_name" value = "{{$data->organization_name}}">
		</div>

		<div>
			<label>Generate Invoice</label>
			<input type = "radio" name = "generate_invoice" value = "yes" @if($data->generate_invoice == 'yes') checked @endif>Yes<input type = "radio" name = "generate_invoice" value = "no" @if($data->generate_invoice == 'no') checked @endif>No
		</div>
		<div>
			<label>Is Active</label>
			<input type = "radio" name = "is_active" value = "yes" @if($data->is_active == 'yes') checked @endif>Yes<input type = "radio" name = "is_active" value = "no" @if($data->is_active == 'no') checked @endif>No
			<input type = "submit" class = "btn btn-success" value = "Edit">
		</div>
	</div>
	{{Form::token()}}
	</form>
@else
	<h1>No Data Found</h1>
@endif

@stop