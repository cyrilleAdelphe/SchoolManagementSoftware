@extends('backend.'.$role.'.main')

@section('content')

	@define $fees = BillingFee::select('fee_category', 'id')->orderBy('fee_category', 'ASC')->get()

	@define $organizations = BillingDiscountOrganization::select('organization_name', 'id')->where('generate_invoice', 'no')->orderBy('organization_name', 'ASC')->get()

	<form action = "{{URL::route('billing-discount-create-flat-discounts-post')}}" method = "post">

		<div class = "form-group">
			<label for = "fee_id">Organization</label>
			<select class = "form-control" name = "organization_id">
			@foreach($organizations as $f)
				<option value = "{{$f->id}}">{{$f->organization_name}}</option>
			@endforeach
			</select>
		</div>

		<div class = "form-group">
			<label for = "discount_name">Discount Name</label>
			<input type = "text" name = "discount_name" class = "form-control">
		</div>

		<div class = "form-group">
			<label for = "fee_id">Fee</label>
			<select class = "form-control" name = "fee_id">
			@foreach($fees as $f)
				<option value = "{{$f->id}}">{{$f->fee_category}}</option>
			@endforeach
			</select>
		</div>

		<div class = "form-group">
			<label for = "discount_percent">Discount Percent</label>
			<input type = "number" step=1 name="discount_percent" class = "form-control">
		</div>

		{{Form::token()}}

		@if(count($organizations))
		<input type = "submit" class = "btn btn-success btn-flat" value = "Create Discount">
		@else
			You don't have organization created to give discounts without generating invoice. Please create organization first. <br>
			<a href = "{{URL::route('billing-discount-organization-create-get')}}" class = "btn btn-success btn-flat">Create Organization</a>
		@endif
	</form>

@stop
