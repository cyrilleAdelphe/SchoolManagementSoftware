@extends('include.form-tabs')

@section('tab-content')

<div class="tab-pane " id="tab_2">
	
	
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
								<td>{{$d->class_name}}</td>
								<td>{{$d->section_code}}</td>
								<td>
									<a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button" @if(AccessController::checkPermission('class-section', 'can_delete') == false) disabled @endif>
						        <i class="fa fa-fw fa-trash"></i>
						      </a>
						  		@include('include.modal-delete')
								</td>
							</tr>
						@endforeach
				
				</tbody>

				{{Form::token()}}
			@else
							<tr>
								<td><div class="alert alert-warning alert-dismissable">
      <h4><i class="icon fa fa-warning"></i>{{$data['message']}}</h4></td>
							</tr>
				@endif
		</table>

	</div>

</div>

@stop

@section('custom-js')

<script src = "{{ asset('backend-js/tableSearch.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/list.js') }}" type = "text/javascript"></script>

@stop
