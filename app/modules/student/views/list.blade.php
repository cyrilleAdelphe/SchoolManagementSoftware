@extends('student.views.form-tabs')

@section('tab-content')

<div class="tab-pane " id="tab_2">

    <div class = "row form-group">
    	<div class = "col-md-3">
    		<label class="label-group">Select Session </label>
    		{{ HelperController::generateSelectList('AcademicSession', 'session_name', 'id', 'session_id', $selected = Input::get('session_id', HelperController::getCurrentSession())) }}
    	</div>

    	<div class = "col-md-3">
    		<label style="color: #fff" class="label-group">Download Excel </label>
    		<br/>
    		<a href = "{{URL::route($module_name.'-export-excel-get')}}">
				<button data-toggle="tooltip" title="" class="btn btn-success btn-flat download-excel" type="button" data-original-title="Download Excel"  @if(!AccessController::checkPermission('student', 'can_view')) disabled @endif>
					<i class="fa fa-fw fa-download"></i> Download Excel
				</button>
			</a>
    	</div>

    </div>
  	<div class = "table-responsive">
      	<table class = 'table table-striped table-hover table-bordered scrollable'>
      		{{$tableHeaders}}
					{{$searchColumns}}
      		@if($data['count'])

				<tbody class = 'search-table'>
				
					<?php $i = 1; ?>
					

						@foreach($data['data'] as $d)

							<tr>
								<td>{{$i++}}</td>
								<td>{{$d->student_name}}</td>
								<td>{{$d->last_name}}</td>
								<td>{{$d->username}}</td>
								<td>{{$d->guardian_contact}}</td>
								<td>{{$d->class_name}}</td>
								<td>{{$d->registered_section_code}}</td>								
								<td>
									<a href = "{{URL::route($module_name.'-view', $d->id)}}" data-toggle="tooltip" title="" class="btn btn-info btn-flat" type="button" data-original-title="View Detail"  @if(!AccessController::checkPermission('student', 'can_view')) disabled @endif><i class="fa fa-fw fa-eye"></i></a>

									<a href = "{{URL::route($module_name.'-edit-get', $d->id)}}" data-toggle="tooltip" title="" class="btn btn-success btn-flat" type="button" data-original-title="Edit"  @if(!AccessController::checkPermission('student', 'can_edit')) disabled @endif><i class="fa fa-fw fa-edit"></i></a>

									<a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button"  @if(!AccessController::checkPermission('student', 'can_delete')) disabled @endif>
						        <i class="fa fa-fw fa-trash"></i>
						      </a>
						  		@include('include.modal-delete')
						  		<a href = "#" class = "btn btn-default change-password" @if(!AccessController::checkPermission('student', 'can_reset_password')) disabled @endif>
						      			<form action = "{{URL::route('system-reset-password')}}" method = "post" class = "change-password-form">
						      				<input type = "hidden" name = "group" value = "student">
						      				<input type = "hidden" name = "user_id" value = "{{$d->id}}">
						      				{{Form::token()}}
						      			</form>
						      			<i class="fa fa-fw fa-trash"></i>
						      		</a>
						      	<a href = "#" class = "btn btn-info change-password" @if(!AccessController::checkPermission('student', 'can_delete')) disabled @endif data-alert-message = "Do you want to deactivate this user">
						      			<form action = "{{URL::route('student-deactive-post')}}" method = "post" class = "change-password-form">
						      				<input type = "hidden" name = "current_session_id" value = "{{ $d->current_session_id }}">
						      				<input type = "hidden" name = "current_class_id" value = "{{ $d->current_class_id }}">
						      				<input type = "hidden" name = "current_section_code" value = "{{ $d->current_section_code }}">
						      				<input type = "hidden" name = "user_id" value = "{{$d->id}}">
						      				{{Form::token()}}
						      			</form>
						      			<i class="fa fa-fw fa-lock"></i>
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
<script src = "{{ asset('backend-js/change-password.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/tableSearch.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/list.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/paginate.js') }}" type = "text/javascript"></script>
<script>
$('#session_id').change(function(e)
{

	window.location = ('{{URL::route("student-list")}}'+'?session_id='+$(this).val());

});

$('.download-excel').click(function(e)
{
	e.preventDefault();
	var session_id = $('#session_id').val();
	var url = $(this).parent().attr('href');
	url += '?session_id=' + session_id;
	window.location = url;
})
</script>
@stop
