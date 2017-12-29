@extends('include.form-tabs')

@section('tab-content')
      
  <div class = "table-responsive">
    <table class = 'table table-striped table-hover table-bordered scrollable'>
			{{$tableHeaders}}
			<tbody class = 'search-table'>
				<?php $i = 1; ?>
				{{$searchColumns}}
				@if($data['count'])
					@foreach($data['data'] as $d)
						<tr>
							<td>{{ $i++ }}</td>
							<td>{{ $d->title }}</td>
							<td>
								@foreach ($d->student_list as $student)
									{{ $student->student_name }}: {{ $student->remarks }}
									<br />
								@endforeach
							</td>
														
							<?php 
								$from = (CALENDAR == 'BS') ? 
															HelperController::formatNepaliDate(substr($d->from_bs, 0, 10)) . ' ' . 
																DateTime::createFromFormat('H:i:s', substr($d->from_ad, 11))->format('g:i A') :
															DateTime::createFromFormat('Y-m-d H:i:s', $d->from_ad)->format('d F Y, g:i A');

								$to = (CALENDAR == 'BS') ? 
									HelperController::formatNepaliDate(substr($d->to_bs, 0, 10)) . ' ' . 
										DateTime::createFromFormat('H:i:s', substr($d->to_ad, 11))->format('g:i A') :
									DateTime::createFromFormat('Y-m-d H:i:s', $d->to_ad)->format('d F Y, g:i A');
							?>
			
							<td>{{ $from }}</td>
							<td>{{ $to }}</td>
							<td>
								<a href = "{{URL::route($module_name.'-push-notification', $d->event_id)}}">
									<button data-toggle="tooltip" title="Push notification" class="btn bg-purple btn-flat" type="button">
	                  <i class="fa fa-fw fa-info"></i>
	                </button>
	              </a>

                <a href = "{{URL::route($module_name.'-view', $d->event_id)}}">
	                <button data-toggle="tooltip" title="View detail" class="btn btn-info btn-flat" type="button" @if(AccessController::checkPermission('extra-activity', 'can_view') == false) disabled @endif >
	                  <i class="fa fa-fw fa-eye"></i>
	                </button>
	              </a>
                
                <a href = "{{URL::route($module_name.'-edit-get', $d->event_id)}}">
									<button data-toggle="tooltip" title="Edit" class="btn btn-success btn-flat" type="button" @if(AccessController::checkPermission('extra-activity', 'can_edit')  == false) disabled @endif >
	                  <i class="fa fa-fw fa-edit"></i>
	                </button>
								</a>
								
								<a href="#" data-toggle="modal" data-target="#delete{{$d->id}}" data-toggle="tooltip" title="Delete">
					        <button data-toggle="tooltip" title="Delete" class="btn btn-danger btn-flat" type="button" @if(AccessController::checkPermission('extra-activity', 'can_delete')  == false) disabled @endif >
	                  <i class="fa fa-fw fa-trash"></i>
	                </button>
					      </a>
					  		@include('include.modal-delete')
							</td>
						</tr>
					@endforeach
					
			@else
				<div class="alert alert-warning alert-dismissable">
					<h4><i class="icon fa fa-warning"></i>No Data Found</h4>
				</div>
			@endif

			</tbody>
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