@extends('include.form-tabs')

@section('tab-content')

<div class = 'container'>

	<div class="table-responsive">
		
		<table class = 'table table-striped table-hover table-bordered'>
			{{$tableHeaders}}
		
				<tbody class = 'search-table'>
				@if($data['count'])
					<?php $i = 1; ?>
					{{$searchColumns}}

						@foreach($data['data'] as $d)
							<tr>
								{{-- <td><input type = 'checkbox' class = 'checkbox_id' name = "rid[]" value = '{{$d->id}}'>{{$i++}}</td> --}}
								<td>{{$i++}}</td>
								<td>{{$d->section_name}}</td>
								<td>{{$d->section_code}}</td>
								<td>
									<a href = "{{URL::route($module_name.'-view', $d->id)}}" data-toggle="tooltip" title="" class="btn btn-info btn-flat" type="button" data-original-title="View Detail" @if(!AccessController::checkPermission('section', 'can_create')) disabled @endif><i class="fa fa-fw fa-eye"></i></a>

									<a href = "{{URL::route($module_name.'-edit-get', $d->id)}}" data-toggle="tooltip" title="" class="btn btn-success btn-flat" type="button" data-original-title="Edit" @if(!AccessController::checkPermission('section', 'can_edit')) disabled @endif><i class="fa fa-fw fa-edit"></i></a>

									<a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button" @if(!AccessController::checkPermission('section', 'can_delete')) disabled @endif>
						        <i class="fa fa-fw fa-trash"></i>
						      </a>
						  		@include('include.modal-delete')
								</td>
							</tr>
						@endforeach
				@else
							<tr>
								<td>{{$data['message']}}</td>
							</tr>
				@endif
				</tbody>
				{{Form::token()}}
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
