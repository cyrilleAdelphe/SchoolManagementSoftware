@extends('backend.'.$role.'.main')

@section('content')


<div class = 'content'>
<div class="bill-title">Discount Manager</div>

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
								<td>{{$d->discount_name}}</td>
								<td>
									<a href = "{{URL::route('billing-discount-view', $d->id)}}"><button data-toggle="tooltip" title="" class="btn btn-info btn-flat" type="button" data-original-title="View Detail" @if(AccessController::checkPermission('billing-discount', 'can_view') == false) disabled @endif><i class="fa fa-fw fa-eye"></i></button></a>
									<a href = "{{URL::route('billing-discount-edit-get', $d->id)}}"><button data-toggle="tooltip" title="" class="btn btn-success btn-flat" type="button" data-original-title="Edit" @if(AccessController::checkPermission('billing-discount', 'can_edit') == false) disabled @endif><i class="fa fa-fw fa-edit"></i></button></a>
									<a href="#" data-toggle="modal" data-target="#discount-delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button">
			                            <i class="fa fa-fw fa-trash"></i>
			                        </a>
			                        @include('billing.views.modal.delete-discount')
                        </td>
                        
						      
						  		
						  	</td>
							</tr>
						@endforeach
				
				</tbody>
				{{Form::token()}}

				@else
  				<div class="bill-alert"><i class="icon fa fa-warning"></i> &nbsp;&nbsp;&nbsp;No Data Found </div>
		
				@endif
			
		</table>

	</div>
	<a href = "{{URL::route('billing-discount-create-get')}}" class = "btn btn-danger btn-flat">Create Discount</a>
	<div class = "content">
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
