@extends('backend.'.$role.'.main')

@section('content')

<div class = 'content'>

	<div class="table-responsive">
		
		<table class = 'table table-striped table-hover table-bordered'>
			@if($data['count'])
				{{$tableHeaders}}
				<tbody class = 'search-table'>
				
					<?php $i = 1; ?>
					{{$searchColumns}}

						@foreach($data['data'] as $d)
							<tr>
								<td>{{$i++}}</td>
								<td>{{$d->financial_year}}-{{$d->invoice_number}}</td>
								<td>{{$d->related_user_group}}</td>
								<td>{{ $d->invoice_group_id }}</td>
								<td>{{json_decode($d->invoice_details)->personal_details->name}}</td>
								<td>{{$d->invoice_balance}}</td>
								<td>{{$d->received_amount}}</td>
								<td>{{$d->is_cleared}}</td>
								<!-- Billing-v1-changed-made-here -->
								<td>{{Carbon\Carbon::CreateFromFormat('Y-m-d H:i:s', $d->issued_date)->format('d M Y')}}</td>
								<td>
								<!-- Billing-v1-changed-made-here -->
			<a href = "{{URL::route('show-invoice-from-invoice-number', [$d->invoice_number])}}?financial_year={{$d->financial_year}}">
				<button data-toggle="tooltip" title="" class="btn btn-info btn-flat" type="button" data-original-title="View Detail">
					<i class="fa fa-fw fa-eye"></i>
				</button>
			</a>
			@if($d->is_cleared == 'no' || $d->is_cleared == 'partial')
			<a href = "{{URL::route('billing-credit-note-get', $d->id)}}">
				<button data-toggle="tooltip" title="" class="btn btn-info btn-flat" type="button" data-original-title="View Detail">Credit Note
					
				</button>
			</a>
			@endif

			@if($d->is_cleared != 'cancel')
			<form method = "post" action = "{{ URL::route('billing-cancel-invoice-post', $d->id) }}">
				<input type = "submit" value = "cancel" class = "btn btn-danger">
				{{Form::token()}}
			</form>
			@endif
						  		</td>
							</tr>
						@endforeach
				
				</tbody>
				{{Form::token()}}
				
				@else
							<div class="alert alert-warning alert-dismissable">
  		<h4><i class="icon fa fa-warning"></i>No Data Found</h4>
		
 	</div>
				@endif
			
		</table>

	</div>

	<div class = "container">
		<div class = 'paginate'>
			@if($data['count'])
										{{$data['data']->appends($queryString)->links()}}
									@endif
		</div>
	</div>
</div>

@stop

@section('custom-js')

<script src = "{{ asset('backend-js/tableSearch.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/list.js') }}" type = "text/javascript"></script>

@stop
