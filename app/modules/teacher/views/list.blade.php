@extends('include.form-tabs')


@section('tab-content')
<p id = "export-to-excel-file_name">Teacher List</p>
<div class="tab-pane " id="tab_2">
    <form method = "post" action = "{{URL::route('global-export-to-excel')}}" id = "export-to-excel-form">
  <a href = "#" class = "btn btn-success" id = "export-to-excel-button">Export To Excel</a>
<br>  
	
	<div style = "display:none">
		<div class = "export-to-excel-row">
			<div class = "export-to-excel-data">SN</div>
			<div class = "export-to-excel-data">Session</div>
			<div class = "export-to-excel-data">Teacher</div>
			<!-- Teacher-show-username-v1-changes-here -->
			<div class = "export-to-excel-data">Username</div>
			<!-- Teacher-show-username-v1-changes-here -->
			<div class = "export-to-excel-data">Class</div>
			<div class = "export-to-excel-data">Section</div>
			<div class = "export-to-excel-data">Class Teacher</div>
		</div>
	</div>
	  <!-- Teacher-show-username-v1-changes-here -->
      <div class="table-responsive">
      <!-- Teacher-show-username-v1-changes-here -->
      <table class = 'table table-striped table-hover table-bordered'>
			@if($data['count'])
			{{$tableHeaders}}
			{{$searchColumns}}
				<tbody class = 'search-table'>
				
					<?php $i = 1; ?>
					

						@foreach($data['data'] as $d)
							<tr class = "export-to-excel-row">
								<td class = "export-to-excel-data">{{$i++}}</td>
								<td class = "export-to-excel-data">{{$d->session_name}}</td>
								<td class = "export-to-excel-data">{{$d->employee_name}}</td>
								<!-- Teacher-show-username-v1-changes-here -->
								<td class = "export-to-excel-data">{{$d->username}}</td>
								<!-- Teacher-show-username-v1-changes-here -->
								<td class = "export-to-excel-data">{{$d->class_name}}</td>
								<td class = "export-to-excel-data">{{$d->section_code}}</td>
								<td class = "export-to-excel-data">{{$d->is_class_teacher}}</td>
								
								<td>
									<a href = "{{URL::route($module_name.'-view', $d->id)}}"><button data-toggle="tooltip" title="" class="btn btn-info btn-flat" type="button" data-original-title="View Detail"><i class="fa fa-fw fa-eye"></i></button></a>
									<a href = "{{URL::route($module_name.'-edit-get', $d->id)}}"><button data-toggle="tooltip" title="" class="btn btn-success btn-flat" type="button" data-original-title="Edit" @if(!AccessController::checkPermission('student', 'can_edit')) disabled @endif><i class="fa fa-fw fa-edit"></i></button></a>
									<a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button" @if(!AccessController::checkPermission('student', 'can_edit')) disabled @endif>
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
							<tr><td><div class="alert alert-warning alert-dismissable">
  		<h4><i class="icon fa fa-warning"></i>No Data Found</h4>
		
 	</div></td></tr>
				@endif
		</table>
		<!-- Teacher-show-username-v1-changes-here -->
		</div>
		<!-- Teacher-show-username-v1-changes-here -->
	</form>
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

<script src = "{{ asset('backend-js/tableSearch.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/list.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/paginate.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/export-to-excel.js') }}" type = "text/javascript"></script>

@stop
