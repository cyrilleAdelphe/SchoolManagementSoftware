@extends('gallery.views.tabs')

@section('tab-content')

<div class="tab-pane " id="tab_2">
	<div class = "table-responsive">
	  <table class = 'table table-striped table-hover table-bordered scrollable'>
			<tbody class = 'search-table'>
					{{$tableHeaders}}
					{{$searchColumns}}
					@if($data['count'])
						@define $i=1
						@foreach($data['data'] as $d)
							<tr>
								<td>{{$i++}}</td>
								<td>{{$d->title}}</td>
								<td>{{$d->description}}</td>
								
								<td>
									<a href="{{URL::route($module_name.'-edit-get', $d->id)}}" class="btn btn-info btn-flat" data-toggle="tooltip" title="EDIT" @if(AccessController::checkPermission('gallery-category', 'can_edit') == false) disabled @endif>
		                <i class="fa fa-fw fa-edit"></i>
		              </a>

		              <a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button" @if(AccessController::checkPermission('gallery-category', 'can_delete') == false) disabled @endif>
		                <i class="fa fa-fw fa-trash"></i>
		              </a>
              		@include($module_name.'.views.delete-modal')
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

<script src = "{{ asset('backend-js/list.js') }}" type = "text/javascript"></script>

@stop