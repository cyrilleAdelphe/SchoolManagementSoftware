@extends('include.form-tabs')

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
			{{$searchColumns}}
			{{-- <form id = "backendListForm" method = "post" action = "{{$queries}}"> --}}
				<tbody class = 'search-table'>
				@if($data['count'])
					@define $i=1
						@foreach($data['data'] as $d)
							<tr>
								{{-- <td><input type = 'checkbox' class = 'checkbox_id minimal' name = "rid[]" value = '{{$d->id}}'></td> --}}
								<td>{{$i++}}</td>
								<td>{{ $d->slide_no }}</td>
								<td>{{$d->title}}</td>
								<td>{{$d->link}}</td>
															
								<td>
									<a href = "{{URL::route($module_name.'-edit-get', $d->id)}}"><button data-toggle="tooltip" title="" class="btn btn-success btn-flat" type="button" data-original-title="Edit"><i class="fa fa-fw fa-edit"></i></button></a>
									<a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button">
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

@stop