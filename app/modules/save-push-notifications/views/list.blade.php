@extends('backend.'.$role.'.main')

@section('content')

<div class = 'content'>

	<div class="table-responsive">
		
		<table class = 'table table-striped table-hover table-bordered'>
			@if($data['count'])
			{{$tableHeaders}}
			{{-- <form id = "backendListForm" method = "post" action = "{{$queries}}"> --}}
				<tbody class = 'search-table'>
				
					<?php $i = 1; ?>
					{{$searchColumns}}

						@foreach($data['data'] as $d)
							<tr>
								{{-- <td><input type = 'checkbox' class = 'checkbox_id' name = "rid[]" value = '{{$d->id}}'></td> --}}
								<td>{{$i++}}</td>
								<td>{{$d->user_group}}</td>
								<td>{{$d->user_id}}</td>
								<td>{{$d->message}}</td>
								<td>
									<a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button" @if(AccessController::checkPermission('academic-session', 'can_delete') == false) disabled @endif>
						        <i class="fa fa-fw fa-trash"></i>
						      </a>
						  		@include('include.modal-delete')
						  	</td>
							</tr>
						@endforeach
				
				</tbody>
				{{Form::token()}}
				{{-- </form> --}}
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
