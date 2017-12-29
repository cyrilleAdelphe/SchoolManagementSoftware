@extends('backend.' . $role . '.main')

@section('content')
	<div class = "content">
	<a href = "{{URL::route('sms-list')}}" class = "btn btn-success">Go Back</a>
	<p>You have {{SmsMainController::makeCurlRequest(MAIN_SERVER_URL.'sms-master/api/get-credits/'.SCHOOL_UNIQUE_ID)}}</p>
	@if(count($data['data']))
		<?php $d = new StdClass;
		$d->message_group_id = $message_group_id; 
		$d->subject = explode('#', $data['data'][0]->message)[0];
		$d->message = explode('#', $data['data'][0]->message)[1];
		$d->user_group = Input::get('user_group', ''); ?>
		<div class = "row">
			<a href="#" data-toggle="modal" data-target="#sendSMS{{$message_group_id}}" data-toggle="tooltip" title="Send SMS" class="btn btn-danger btn-flat" type="button"  @if(!AccessController::checkPermission('sms', 'can_create')) disabled @endif>
			Resend			    
			</a>
		</div>
		@include('sms.views.send-sms-modal')
	@endif
	</div>
	
	<div class = "table-responsive">
  	<table class = 'table table-striped table-hover table-bordered scrollable'>
  		<thead>
  			<tr>
  				<th>SN</th>
  				<th>Sent to</th>
  				<th>Sent By</th>
  				<th>Message</th>
  				<th>Status</th>
  				<th>Sent at</th>
  			</tr>
  		</thead>
  		
  		@if(count($data['data']))
	  		@define $sender_role = $data['data'][0]->sender_role;
	  		

	  		@if($sender_role == 'superadmin')
	  			<?php $sender_name = HelperController::pluckFieldFromId('SuperAdmin', 'name', $data['data'][0]->sender_id, $not_id = false, $search_field_name = '') ?>
	  		@elseif($sender_role == 'admin')
	  			<?php $sender_name = HelperController::pluckFieldFromId('Employee', 'emplyee_name', $data['data'][0]->sender_id, $not_id = false, $search_field_name = '') ?>
	  		@elseif($sender_role == 'guardian')
	  			<?php $sender_name = HelperController::pluckFieldFromId('Guardian', 'guardian_name', $data['data'][0]->sender_id, $not_id = false, $search_field_name = '') ?>
	  		@elseif($sender_role == 'student')
	  			<?php $sender_name = HelperController::pluckFieldFromId('StudentRegistration', 'student_name', $data['data'][0]->sender_id, $not_id = false, $search_field_name = '') ?>
	  		@endif

	  		<tbody class = 'search-table'>

				<?php $i = 1; ?>
					@foreach($data['data'] as $d)
						<tr>
							<td>{{$i++}}</td>
							<td>{{$d->name}} ({{$d->phone_no == NULL ? 'sms not sent' : $d->phone_no}})</td>
							<td>{{$sender_name}} ({{$sender_role}})</td>
							<td>{{SMSHelperController::getOnlyMessage($d->message)}}</td>
							<td>{{$d->sms_status == NULL ? 'sms not sent' : $d->sms_status}}</td>
							<td>{{ DateTime::createFromFormat('Y-m-d H:i:s', $d->created_at)->format('d F Y, g:i A') }}</td>
						</tr>
					
				@endforeach
				</tbody>
			@else
				<div class="alert alert-warning alert-dismissable">
  				<h4>
  					<i class="icon fa fa-warning"></i>{{$data['message']}}
  				</h4>
				</div>
			@endif
		</table>
	</div>

@stop

@section('custom-js')

<script src = "{{ asset('backend-js/tableSearch.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/actionButtons.js') }}" type = "text/javascript"></script>

<script src = "{{ asset('backend-js/list.js') }}" type = "text/javascript"></script>

@stop

