@extends('backend.'.$role.'.main')

@section('content')

{{$actionButtons}}

{{-- this block is for hidden values --}}

{{$paginateBar}}

<div class = "container">
	<select id = 'list_status'>
		<option value = 'yes' @if(isset($queryString['status']) && $queryString['status'] == 'yes') selected @endif>Live Data</option>
		<option value = 'no' @if(isset($queryString['status']) && $queryString['status'] == 'no') selected @endif>Deleted Data</option>
	</select>
</div>
<div class = "container">
	<a class = "btn btn-info" href = '{{URL::current()}}'>Cancel Query</a>
</div>


<div class = 'container'>

	<div class="table-responsive">
		<input type = "checkbox" id = "PraCheckAll"><br>
		<table class = 'table table-striped table-hover table-bordered'>
			{{$tableHeaders}}
			<form id = "backendListForm" method = "post" action = "{{$queries}}">
				<tbody class = 'search-table'>
				@if($data['count'])
					<?php $i = 1; ?>
					{{$searchColumns}}

						@foreach($data['data'] as $d)
							<tr>
								<td><input type = 'checkbox' class = 'checkbox_id' name = "rid[]" value = '{{$d->id}}'>{{$i++}}</td>
								<td>{{$d->position_name}}</td>
								<td><a href = "{{URL::route($module_name.'-view', $d->id)}}">view</a></td>
								<td><a href = "{{URL::route($module_name.'-edit-get', $d->id)}}">edit</a></td>
							</tr>
						@endforeach
				@else
							<tr>
								<td>{{$data['message']}}</td>
							</tr>
				@endif
				</tbody>
				{{Form::token()}}
			</form>
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

<div class = "container">
	{{TemplateController::getTemplate('home_page_block_1')}}
</div>

@stop


@section('custom-js')

<script src = "{{ asset('backend-js/tableSearch.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/list.js') }}" type = "text/javascript"></script>

@stop
