@extends('guardian.views.form-tabs')

@section('tab-content')

<div class="tab-pane " id="tab_2">
  	<div class = "table-responsive">
      	<table class = 'table table-striped table-hover table-bordered scrollable'>
			@if($data['count'])
			{{$tableHeaders}}
			
				<tbody class = 'search-table'>
				
					<?php $i = 1; ?>
					{{$searchColumns}}

						@foreach($data['data'] as $d)
							<tr>
								<td>{{$i++}}</td>
								<td>{{$d->guardian_name}} - {{$d->username}}</td>
								<td>{{$d->student_name}}</td>
								<td>{{$d->class_name}}</td>
								<td>{{$d->primary_contact}} <br/> {{$d->secondary_contact}}</td>
								<td>
									<a href = "{{URL::route($module_name.'-view', $d->id)}}" data-toggle="tooltip" title="" class="btn btn-info btn-flat" type="button" data-original-title="View Detail" @if(AccessController::checkPermission('secretary', 'can_view') == false) disabled @endif><i class="fa fa-fw fa-eye"></i></a>

									<a href = "{{URL::route($module_name.'-edit-get', $d->id)}}" data-toggle="tooltip" title="" class="btn btn-success btn-flat" type="button" data-original-title="Edit" @if(AccessController::checkPermission('secretary', 'can_edit') == false) disabled @endif><i class="fa fa-fw fa-edit"></i></a>

									<a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button" @if(AccessController::checkPermission('secretary', 'can_delete') == false) disabled @endif>
						        <i class="fa fa-fw fa-trash"></i>
						      </a>
						  		@include('include.modal-delete')

						  		<a href = "#" class = "btn btn-default change-password" @if(!AccessController::checkPermission('guardian', 'can_reset_password')) disabled @endif>
						      			<form action = "{{URL::route('system-reset-password')}}" method = "post" class = "change-password-form">
						      				<input type = "hidden" name = "group" value = "guardian">
						      				<input type = "hidden" name = "user_id" value = "{{$d->id}}">
						      				{{Form::token()}}
						      			</form>
						      			<i class="fa fa-fw fa-trash"></i>
						      		</a>

								</td>
							</tr>
						@endforeach
				
				</tbody>
				
			@else
							<div class="alert alert-warning alert-dismissable">
  		<h4><i class="icon fa fa-warning"></i>No Data Found</h4>
		
 	</div>
				@endif
		</table>
	</div>
</div> 

	<div class = "container">
		<div class = 'paginate'>
			@if($data['count'])
			{{$data['guardian_id']->appends($queryString)->links()}}
			@endif
		</div>
	</div>


@stop

@section('custom-js')

<script src = "{{ asset('backend-js/tableSearch.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/list.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/change-password.js') }}" type = "text/javascript"></script>

@stop
