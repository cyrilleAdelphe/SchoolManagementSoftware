@extends('backend.'.$role.'.main')

@section('content')
	<h1>Assign Fee To Discount</h1>
	<form method = "post" action = "{{URL::route('billing-assign-fee-to-discount-post')}}">
	<div class = "row">
		<div class="form-group">
			<label>Fee Name</label>
			<?php
				$fees = BillingFee::select('fee_category', 'id')
									->orderBy('fee_category', 'ASC')
									->get();
			?>
			<select name="fee_id" class = 'form-control'>
				@foreach($fees as $f)
					<option value="{{ $f->id }}">{{ $f->fee_category }}</option>
				@endforeach
			</select>
		</div>

		<div class="form-group">
			<label>Discount Name</label>
			<?php
				$discounts = BillingDiscount::select('id', 'discount_name')
											->orderBy('discount_name', 'ASC')
											->get();
			?>
			<select name="discount_id" class = 'form-control'>
				@foreach($discounts as $f)
					<option value="{{ $f->id }}">{{ $f->discount_name }}</option>
				@endforeach
			</select>
		</div>

		<div class="form-group">
			<label>Discounted Percent</label>
			<input type="number" name="discount_percent" required>
		</div>
	</div>
	{{Form::token()}}
		<input type="submit" class="btn btn-flat btn-info" value="Add">
	</form>

@stop