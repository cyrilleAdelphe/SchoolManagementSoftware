@extends('events.views.backend')

@section('page-header')
	<h1>Manage/View Events</h1>
@stop

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
						{{-- <td><input type = 'checkbox' class = 'checkbox_id minimal' name = "rid[]" value = '{{$d->id}}'></td> --}}
						<td>{{$i++}}</td>
						<td>
							<a href="#" data-toggle="modal" data-target="#detail{{$i}}">
								{{$d->title}}
              </a>
							<div id="detail{{$i}}" class="modal fade" role="dialog">
                <div class="modal-dialog">

                  <!-- Modal content-->
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h4 class="modal-title">
                      	{{$d->title}}
                      </h4>
                    </div>
                    <div class="modal-body">
                      <p>{{$d->description}}</p>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                  </div>

                </div>
              </div>
						</td>
						<td>{{$d->event_code}}</td>
						<td>{{HelperController::underscoreToSpace($d->event_type)}}</td>
						<td>
							@define $for = array()
							@if($d->for_students === 'yes')
								@define $for[] = 'students'
							@endif
							@if($d->for_teachers === 'yes')
								@define $for[] = 'teachers'
							@endif
							@if($d->for_management_staff === 'yes')
								@define $for[] = 'management staff'
							@endif
							@if($d->for_parents === 'yes')
								@define $for[] = 'parents'
							@endif
							@if (count($for) == 4)
								@define $for = 'all'
							@else
								@define $for = implode(',', $for);
							@endif
							{{$for}}
						</td>
						<td>{{DateTime::createFromFormat('Y-m-d H:i:s',$d->from_ad)->format('d F Y, g:i A')}}<br/>(Nep: {{HelperController::formatNepaliDate(substr($d->from_bs,0,10))}})</td>
						<td>{{DateTime::createFromFormat('Y-m-d H:i:s',$d->to_ad)->format('d F Y, g:i A')}}<br/>(Nep: {{HelperController::formatNepaliDate(substr($d->to_bs,0,10))}})</td>
														
						<td>
							<a href="{{URL::route($module_name . '-send-notification', $d->id)}}" title="Remind" class="btn btn-info btn-flat bg-purple" type="button" data-toggle="tooltip"><i class="fa fa-fw fa-info"></i></a>
							<a href = "{{URL::route($module_name.'-view', $d->id)}}"><button data-toggle="tooltip" title="" class="btn btn-success btn-flat" type="button" data-original-title="View Detail"><i class="fa fa-fw fa-list-ul"></i></button></a>
							<a href = "{{URL::route($module_name.'-edit-get', $d->id)}}"><button data-toggle="tooltip" title="" class="btn btn-info btn-flat" type="button" data-original-title="Edit"><i class="fa fa-fw fa-edit"></i></button></a>
							<a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button">
				        <i class="fa fa-fw fa-trash"></i>
				      </a>
				      @include('include.modal-delete')
				  	</td>
					</tr>
					
				@endforeach
		
		</tbody>
		{{Form::token()}}
		
		@else
    <tr><td>
    	<div class="alert alert-warning alert-dismissable">
    		<h4><i class="icon fa fa-warning"></i>No Data Found</h4>
  		</div>
		</td></tr>

     @endif
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

<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/list.js') }}" type = "text/javascript"></script>

@stop
