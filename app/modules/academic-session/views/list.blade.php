@extends('academic-session.views.form-tabs')

@section('tab-content')

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
								<td>{{$d->session_name}}</td>
								<td>{{DateTime::createFromFormat('Y-m-d',$d->session_start_date_in_ad)->format('d F Y')}}</td>
								<td>{{HelperController::formatNepaliDate($d->session_start_date_in_bs)}}</td>
								<td>{{DateTime::createFromFormat('Y-m-d',$d->session_end_date_in_ad)->format('d F Y')}}</td>
								<td>{{HelperController::formatNepaliDate($d->session_end_date_in_bs)}}</td>
								<td>
									<a href = "{{URL::route($module_name.'-view', $d->id)}}" data-toggle="tooltip" title="" class="btn btn-info btn-flat" type="button" data-original-title="View Detail" @if(AccessController::checkPermission('academic-session', 'can_view') == false) disabled @endif><i class="fa fa-fw fa-eye"></i></a>

									<a href = "{{URL::route($module_name.'-edit-get', $d->id)}}" data-toggle="tooltip" title="" class="btn btn-success btn-flat" type="button" data-original-title="Edit" @if(AccessController::checkPermission('academic-session', 'can_edit') == false) disabled @endif><i class="fa fa-fw fa-edit"></i></a>
{{--
									<a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button" @if(AccessController::checkPermission('academic-session', 'can_delete') == false) disabled @endif>
						        <i class="fa fa-fw fa-trash"></i>
						      </a>
						  		@include('include.modal-delete')
						  		--}}
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
