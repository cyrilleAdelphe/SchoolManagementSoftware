@extends('include.form-tabs')

@section('tab-content')

{{-- $actionButtons --}}

{{-- this block is for hidden values --}}
{{-- $paginateBar --}}

{{-- <div class = "container">
	<select id = 'list_status'>
		<option value = 'yes' @if(isset($queryString['status']) && $queryString['status'] == 'yes') selected @endif>Live Data</option>
		<option value = 'no' @if(isset($queryString['status']) && $queryString['status'] == 'no') selected @endif>Deleted Data</option>
	</select>
</div>
<div class = "container">
	<a class = "btn btn-info" href = '{{URL::current()}}'>Cancel Query</a>
</div>
 --}}

<div class = 'content'>

	<div class="table-responsive">
		{{-- <input type = "checkbox" id = "PraCheckAll"><br> --}}
		<table class = 'table table-striped table-hover table-bordered'>
			{{$tableHeaders}}
			{{-- <form id = "backendListForm" method = "post" action = "{{$queries}}"> --}}
				<tbody class = 'search-table'>
				@if($data['count'])
					<?php $i = 1; ?>
					{{$searchColumns}}

						@foreach($data['data'] as $d)
							<tr>
								<td>
									{{-- <input type = 'checkbox' class = 'checkbox_id' name = "rid[]" value = '{{$d->id}}'> --}}
									{{$i++}}
								</td>
								<td>{{$d->class_name}}</td>
								<td>{{$d->subject_code}}</td>
								<td>
									<a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button" @if(AccessController::checkPermission('class-subject', 'can_delete') == false) disabled @endif>
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
			{{-- </form> --}}
		</table>

		<div class = "container">
			<div class = 'paginate'>
				@if($data['count'])
					{{$data['data']->appends($queryString)->links()}}
				@endif
			</div>
		</div>

	</div>
</div>

@stop

@section('custom-js')

<script src = "{{ asset('backend-js/tableSearch.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/list.js') }}" type = "text/javascript"></script>

@stop
