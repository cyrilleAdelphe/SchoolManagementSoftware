@extends('employee.views.form-tabs')

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
								<td>{{$d->employee_name}}</td>							
								<td>{{$d->username}}</td>							
								<td>{{$d->current_address}}</td>
								<td>{{$d->primary_contact}} <br/> {{$d->secondary_contact}}</td>
								<td>
									@if(HelperController::validateDate($d->joining_date_in_ad, 'Y-m-d'))
									B.S. {{HelperController::formatNepaliDate($d->joining_date_in_bs)}} <br/> 
									<span class="text-green">A.D. {{DateTime::createFromFormat('Y-m-d', $d->joining_date_in_ad)->format('d F Y')}}</span></td>
									@endif
								

								<td>
									<a href = "{{URL::route($module_name.'-view', $d->id)}}"button data-toggle="tooltip" title="" class="btn btn-info btn-flat" type="button" data-original-title="View Detail"  @if(AccessController::checkPermission('employee', 'can_view') == false) disabled @endif><i class="fa fa-fw fa-eye"></i></a>

									<a href = "{{URL::route($module_name.'-edit-get', $d->id)}}" data-toggle="tooltip" title="" class="btn btn-success btn-flat" type="button" data-original-title="Edit" @if(AccessController::checkPermission('employee', 'can_edit') == false) disabled @endif><i class="fa fa-fw fa-edit"></i></a>

									<a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button" @if(AccessController::checkPermission('employee', 'can_delete') == false) disabled @endif>
						        <i class="fa fa-fw fa-trash"></i>
						      </a>
						  		@include('include.modal-delete')

						  		<a href = "#" class = "btn btn-default change-password" @if(!AccessController::checkPermission('employee', 'can_reset_password')) disabled @endif>
						      			<form action = "{{URL::route('system-reset-password')}}" method = "post" class = "change-password-form">
						      				<input type = "hidden" name = "group" value = "admin">
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
		<select class = "paginate_list">
			<option value = "10" @if(isset($queryString['paginate']) && $queryString['paginate']==10) selected @endif>10</option>
			<option value = "20" @if(isset($queryString['paginate']) && $queryString['paginate']==20) selected @endif>20</option>
			<option value = "30" @if(isset($queryString['paginate']) && $queryString['paginate']==30) selected @endif>30</option>
			
		</select>
	</div>
	<div class = "container">
		<div class = 'paginate'>
			@if($data['count'])
				{{$data['data']->appends($queryString)->links()}}
			@endif
		</div>
	</div>


@stop

@section('custom-js')

<script src = "{{ asset('backend-js/paginate.js') }}" type = "text/javascript"></script>
<script src = "{{ asset('backend-js/tableSearch.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/list.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/change-password.js') }}" type = "text/javascript"></script>

@stop
