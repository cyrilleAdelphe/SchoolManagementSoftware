@extends('backend.'.$role.'.contact_us_manager')

@section('tab-content')
	
	{{-- $actionButtons --}}

	{{-- <section>
    <div class="col-md-2">
        <div class="form-group">
          <select class="form-control" id = "list_status">
            <option value = "yes" @if(isset($queryString['status']) && $queryString['status'] == 'yes') selected @endif>Live Data</option>
            <option value = "no" @if(isset($queryString['status']) && $queryString['status'] == 'no') selected @endif>Deleted Data</option>
          </select>
        </div>
    </div>

    {{$paginateBar}}

    <div class="col-md-2">
      <a  href = '{{URL::current()}}'><button class="btn btn-block btn-danger">Cancel Query</button></a>
    </div>
  </section> --}}
      
      <table class = 'table table-striped table-hover table-bordered'>
			{{$tableHeaders}}
			{{-- <form id = "backendListForm" method = "post" action = "{{$queries}}"> --}}
				<tbody class = 'search-table'>
				@if($data['count'])
					<?php $i = 1; ?>
					{{$searchColumns}}

						@foreach($data['data'] as $d)
							<tr>
								{{-- <td><input type = 'checkbox' class = 'checkbox_id minimal' name = "rid[]" value = '{{$d->id}}'></td> --}}
								<td>{{$i++}}</td>
								<td>{{$d->sender_email}}</td>
								<td>{{$d->sender_location}}</td>
								<td>{{$d->subject}}</td>
								<td>{{$d->query}}</td>
								
								<td>
									<a href = "{{URL::route($module_name.'-view', $d->id)}}"><button data-toggle="tooltip" title="" class="btn btn-info btn-flat" type="button" data-original-title="View Detail" @if(AccessController::checkPermission('contact-us', 'can_view') == false) disabled @endif><i class="fa fa-fw fa-eye"></i></button></a>
									<a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button" @if(AccessController::checkPermission('contact-us', 'can_delete') == false) disabled @endif>
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


@stop

@section('custom-js')

<script src = "{{asset('public/backend-js/tableSearch.js') }}" type = "text/javascript"></script>

<script src = "{{asset('public/backend-js/actionButtons.js') }}" type = "text/javascript"></script>

<script src = "{{asset('public/backend-js/list.js') }}" type = "text/javascript"></script>

@stop
