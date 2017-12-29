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
								<td>{{$d->financial_year}}</td>
								<td>{{$d->receipt_no}}</td>
								<td>{{$d->invoice_no}}</td>
								<td>{{$d->paid_amount}}</td>
								<td>{{$d->amount_to_be_paid}}</td>
								<td>{{$d->received_from}}</td>
								<td>@if($d->received_from == 'student'){{$d->student_name}} @elseif($d->received_from == 'organization') {{$d->organization_name}} @else {{ $d->received_name }} @endif</td>
								<td>{{$d->received_on}}</td>
								<td>{{$d->created_by}}</td>
								<td>
			<a href = "{{URL::route('billing-view-receipt-from-receipt-id', $d->id)}}">
				<button data-toggle="tooltip" title="" class="btn btn-info btn-flat" type="button" data-original-title="View Detail">
					<i class="fa fa-fw fa-eye"></i>
				</button>
			</a>
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
