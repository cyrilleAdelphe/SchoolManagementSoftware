@extends('backend.' . $role . '.main')

@section('content')
	
	<div class = "row">
		<div class = "col-md-9">
			You have {{$available_credits}} SMS Credits <input type = "hidden" id = "eton_sms_credits" value = "{{$available_credits}}">
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
					@if($d->unseen_users > 0)
						<tr>
							<td>{{$i++}}</td>
							<td>{{$d->user_group}}</td>
							<td>{{$d->unseen_users}}</td>
							<td>{{$d->seen_users}}</td>
							<td>{{$d->subject}}</td>
							<td>{{$d->message}}</td>
							<td>{{ DateTime::createFromFormat('Y-m-d H:i:s', $d->created_at)->format('d F Y, g:i A') }}</td>
							<td>
								<a href="#" data-toggle="modal" data-target="#sendSMS{{$d->message_group_id}}" data-toggle="tooltip" title="Send SMS" class="btn btn-danger btn-flat" type="button"  @if(!AccessController::checkPermission('sms', 'can_create')) disabled @endif>
					        		<i class="fa fa-fw fa-eye"></i>
					      		</a>
					      		<a href="{{URL::route('sms-view', array($d->message_group_id))}}?user_group={{$d->user_group}}" class="btn btn-danger btn-flat" type="button"  @if(!AccessController::checkPermission('sms', 'can_view')) disabled @endif>
					        		<i class="fa fa-fw fa-message"></i>
					      		</a>
					  		@include('sms.views.send-sms-modal')
							</td>
						</tr>
					@endif
				@endforeach
				</tbody>
			@else
				<div class="alert alert-warning alert-dismissable">
  				<h4>
  					<i class="icon fa fa-warning"></i>No Data Found
  				</h4>
				</div>
			@endif
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

<script type="text/javascript">
	$('#6').attr('placeholder', 'yyyy-mm-dd hh:mm:ss');

	$('#2').attr('disabled', true);
	$('#3').attr('disabled', true);
</script>
@stop

